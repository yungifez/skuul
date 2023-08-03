<?php

namespace App\Casts;

use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Money implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): BrickMoney
    {
        if (is_null($value)) {
            return null;
        }

        return BrickMoney::ofMinor($value, config('app.currency'));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (!$value instanceof BrickMoney) {
            return BrickMoney::of($value, config('app.currency'))->getMinorAmount()->toInt();
        }

        return $value->getMinorAmount()->toInt();
    }
}
