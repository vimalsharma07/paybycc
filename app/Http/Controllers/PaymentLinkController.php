<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentLinkRequest;
use App\Models\PaymentLink;
use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class PaymentLinkController extends Controller
{
    public function __construct(
        protected PaymentLinkService $paymentLinks,
    ) {}

    public function index(): View|RedirectResponse
    {
        $seller = request()->user();

        if (! $seller->canCreatePaymentLinks()) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Payment links are available for active freelancer accounts.');
        }

        $links = PaymentLink::query()
            ->where('seller_id', $seller->id)
            ->with(['order', 'latestPayment'])
            ->latest()
            ->paginate(15);

        return view('payment-links.index', [
            'paymentLinks' => $links,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $seller = request()->user();

        if (! $seller->canCreatePaymentLinks()) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Payment links are available for active freelancer accounts.');
        }

        return view('payment-links.create', [
            'minAmount' => (float) config('platform.marketplace.min_order_amount', 1),
            'maxAmount' => (float) config('platform.marketplace.max_order_amount', 500000),
            'defaultExpiryDays' => (int) config('platform.payment_links.default_expiry_days', 30),
            'maxExpiryDays' => (int) config('platform.payment_links.max_expiry_days', 90),
        ]);
    }

    public function store(StorePaymentLinkRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $seller = $request->user();

        $description = isset($validated['description']) ? trim((string) $validated['description']) : null;
        $expiresAt = $this->resolveExpiry($validated);

        $amount = $request->isOpenAmount()
            ? null
            : number_format((float) $validated['amount'], 2, '.', '');

        try {
            $paymentLink = $this->paymentLinks->create(
                $seller,
                $amount,
                $description !== '' ? $description : null,
                $expiresAt,
                $request->maxUses(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()
            ->route('payment-links.show', $paymentLink)
            ->with('status', 'Payment link created — share it with your client.');
    }

    public function show(PaymentLink $paymentLink): View
    {
        $this->authorizeSeller($paymentLink);

        $paymentLink->load(['order', 'latestPayment']);

        return view('payment-links.show', [
            'paymentLink' => $paymentLink,
        ]);
    }

    public function cancel(PaymentLink $paymentLink): RedirectResponse
    {
        $this->authorizeSeller($paymentLink);

        try {
            $this->paymentLinks->cancel($paymentLink, request()->user());
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['payment_link' => $e->getMessage()]);
        }

        return back()->with('status', 'Payment link cancelled.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function resolveExpiry(array $validated): ?\DateTimeInterface
    {
        if (! empty($validated['expires_on'])) {
            return \Carbon\Carbon::parse($validated['expires_on'])->endOfDay();
        }

        $expiryDays = isset($validated['expires_in_days']) ? (int) $validated['expires_in_days'] : 0;

        if ($expiryDays > 0) {
            return now()->addDays($expiryDays);
        }

        $defaultDays = (int) config('platform.payment_links.default_expiry_days', 30);

        return $defaultDays > 0 ? now()->addDays($defaultDays) : null;
    }

    protected function authorizeSeller(PaymentLink $paymentLink): void
    {
        abort_unless((int) $paymentLink->seller_id === (int) auth()->id(), 403);
    }
}
