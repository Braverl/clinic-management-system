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
            if (empty($model->{$model->getBusinessNumberColumn()})) {
                $model->{$model->getBusinessNumberColumn()} = static::generateBusinessNumber($prefix);
            }
        });
    }

    protected function getBusinessNumberColumn(): string
    {
        return static::$businessNumberColumn ?? 'number';
    }
}