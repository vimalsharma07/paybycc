<?php

namespace App\Services\Kyc;

use App\Models\PanVerification;
use App\Models\User;
use App\Services\Logging\FlowLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PanVerificationService
{
    public function __construct(
        protected ApitxtPanVerifier $verifier,
        protected FlowLog $flow,
    ) {}

    public function verify(string $pan, string $name, string $dob): PanVerificationResult
    {
        $pan = strtoupper($pan);

        $cached = PanVerification::query()
            ->where('pan', $pan)
            ->where('status', PanVerification::STATUS_VERIFIED)
            ->first();

        if ($cached !== null) {
            $raw = $cached->decodedResponse();

            return PanVerificationResult::success(
                $raw,
                is_array($raw['data'] ?? null) ? $raw['data'] : [],
                fromCache: true,
            );
        }

        $result = $this->verifier->verify($pan, $name, $dob);

        if ($result->ok) {
            $this->storeVerification($pan, $result->raw);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    protected function storeVerification(string $pan, array $raw): void
    {
        PanVerification::query()->updateOrCreate(
            ['pan' => $pan],
            [
                'response' => PanVerification::compressResponse($raw),
                'status' => PanVerification::STATUS_VERIFIED,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $apiData
     */
    public function applyToUser(User $user, string $pan, string $panName, string $dobInput, array $apiData, bool $hasGst = false, ?string $gstin = null): void
    {
        DB::transaction(function () use ($user, $pan, $panName, $dobInput, $apiData, $hasGst, $gstin) {
            $user->pan = strtoupper($pan);
            $user->pan_name = $panName;
            $user->dob = $this->parseDob($dobInput);
            $user->pan_type = $this->normalizePanType($apiData['category'] ?? null);
            $user->isgst_available = $hasGst;
            $user->gstin = $hasGst && $gstin !== null && $gstin !== '' ? strtoupper($gstin) : null;

            $this->applyAddressFromApi($user, $apiData);

            if ($this->isCompanyPanType($user->pan_type)) {
                $user->company_name = $this->resolveCompanyName($apiData, $panName);
            }

            $user->kyc_status = User::KYC_ACTIVE;
            $user->kyc_skipped_at = null;
            $user->save();
        });
    }

    public function assertPanNotUsedByAnotherUser(string $pan, User $user): ?string
    {
        $exists = User::query()
            ->where('pan', strtoupper($pan))
            ->where('id', '!=', $user->id)
            ->exists();

        return $exists
            ? 'This PAN is already linked to another account.'
            : null;
    }

    protected function parseDob(string $dob): ?string
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $dob)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function normalizePanType(mixed $category): ?string
    {
        $type = strtolower(trim((string) $category));

        return $type !== '' ? $type : null;
    }

    protected function isCompanyPanType(?string $panType): bool
    {
        if ($panType === null || $panType === '') {
            return false;
        }

        return ! in_array($panType, ['individual', 'person'], true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolveCompanyName(array $data, string $fallback): string
    {
        foreach (['name', 'registered_name', 'full_name', 'holder_name'] as $key) {
            $value = trim((string) ($data[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function applyAddressFromApi(User $user, array $data): void
    {
        $address = trim((string) ($data['address'] ?? $data['full_address'] ?? $data['address_line1'] ?? ''));
        if ($address !== '' && empty($user->address_line1)) {
            $user->address_line1 = mb_substr($address, 0, 255);
        }

        $line2 = trim((string) ($data['address_line2'] ?? ''));
        if ($line2 !== '' && empty($user->address_line2)) {
            $user->address_line2 = mb_substr($line2, 0, 255);
        }

        $city = trim((string) ($data['city'] ?? ''));
        if ($city !== '' && empty($user->city)) {
            $user->city = mb_substr($city, 0, 80);
        }

        $state = trim((string) ($data['state'] ?? ''));
        if ($state !== '' && empty($user->state)) {
            $user->state = mb_substr($state, 0, 80);
        }

        $pincode = trim((string) ($data['pincode'] ?? $data['pin_code'] ?? $data['zip'] ?? ''));
        if ($pincode !== '' && empty($user->pincode)) {
            $user->pincode = mb_substr(preg_replace('/\D/', '', $pincode) ?: $pincode, 0, 10);
        }
    }
}
