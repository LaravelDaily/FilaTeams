<?php

namespace LaravelDaily\FilaTeams\Concerns;

use Illuminate\Support\Str;

trait GeneratesUniqueTeamSlugs
{
    public static function bootGeneratesUniqueTeamSlugs(): void
    {
        static::creating(function ($model) {
            $model->slug = static::generateUniqueSlug($model->name);
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = static::generateUniqueSlug($model->name, $model->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (static::withTrashed()->where('slug', $slug)->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
