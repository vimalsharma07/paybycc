<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanVerification extends Model
{
    public const STATUS_VERIFIED = 'verified';

    public const STATUS_INVALID = 'invalid';

    protected $fillable = [
        'pan',
        'response',
        'status',
    ];

    /**
     * @return array<string, mixed>
     */
    public function decodedResponse(): array
    {
        if ($this->response === null || $this->response === '') {
            return [];
        }

        $binary = base64_decode($this->response, true);
        if ($binary === false) {
            return [];
        }

        $json = @gzuncompress($binary);
        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function compressResponse(array $payload): string
    {
        $compressed = gzcompress(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            6,
        );

        return base64_encode($compressed);
    }
}
