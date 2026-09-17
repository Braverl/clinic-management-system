<?php

namespace App\Concerns;

use Illuminate\Support\Str;

trait GeneratesBusinessNumber
{
    protected static function generateBusinessNumber(string $prefix): string
    {
        return $prefix . '-' . strtoupper(Str::random(10));
    }

    protected static function bootGeneratesBusinessNumber(): void
    {
        $prefix = static::$businessNumberPrefix ?? null;

        if (!$prefix) {
            return;
        }

        static::creating(function ($model) use ($prefix) {
            $column = $model->getBusinessNumberColumn();

            if (!empty($model->{$column})) {
                return;
            }

            $model->{$column} = static::generateUniqueBusinessNumber($prefix);
        });
    }

    protected static function generateUniqueBusinessNumber(string $prefix): string
    {
        $column = (new static)->getBusinessNumberColumn();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = static::generateBusinessNumber($prefix);

            if (!static::query()->where($column, $candidate)->exists()) {
                return $candidate;
            }
        }

        return static::generateBusinessNumber($prefix);
    }

    protected function getBusinessNumberColumn(): string
    {
        return static::$businessNumberColumn ?? 'number';
    }
}