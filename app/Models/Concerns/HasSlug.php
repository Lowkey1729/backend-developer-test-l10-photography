<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
}
