<?php

namespace App\Casts;

use Brick\Money\Money as BrickMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Money implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): BrickMoney
    {
        return BrickMoney::ofMinor($value, config('app.currency'));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof BrickMoney) {
            return $value;
        }
        return $value->getMinorAmount()->toInt();
    }
}
