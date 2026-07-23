<?php

namespace App\Models;

use App\Enums\CaptureStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceTagCapture extends Model
{
    protected $fillable = [
        'store_id', 'image_path', 'ocr_raw_text', 'ocr_parsed_json',
        'parsed_item_number', 'parsed_name', 'parsed_amount_minor',
        'parsed_currency_code', 'parsed_at', 'status',
    ];

    protected $casts = [
        'ocr_parsed_json'    => 'array',
        'parsed_at'          => 'datetime',
        'status'             => CaptureStatus::class,
        'parsed_amount_minor'=> 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function parsedAmountDecimal(): ?float
    {
        return $this->parsed_amount_minor !== null
            ? $this->parsed_amount_minor / 100
            : null;
    }
}
