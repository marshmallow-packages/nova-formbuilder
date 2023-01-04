<?php

namespace Marshmallow\NovaFormbuilder\Enums\Traits;

use Illuminate\Support\Collection;

trait Mutators
{
    public static function allOptionsAsArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->title();
        }
        return $array;
    }

    public static function allOptionsAsCollection(): Collection
    {
        return collect(
            self::allOptionsAsArray()
        );
    }

    public static function allOptionsForBadge(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->badge();
        }
        return
            array_merge([
                '-' => '',
            ], $array);
    }
}
