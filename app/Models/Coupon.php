<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'valid_until',
        'usage_limit',
        'usage_count',
        'is_active'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'float',
        'min_order_value' => 'float',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($subtotal): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_order_value && $subtotal < $this->min_order_value) {
            return 0;
        }

        if ($this->type === 'percent') {
            return ($this->value / 100) * $subtotal;
        }

        return min($this->value, $subtotal);
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
