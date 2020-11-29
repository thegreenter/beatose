<?php

declare(strict_types=1);

namespace App\Services\Util;

class Mapper
{
    public function map(object $data, string $classType): object
    {
        $target = new $classType();
        $props = get_class_vars($classType);

        foreach ($props as $key => $value) {
            if (property_exists($data, $key)) {
                $target->$key = $data->$key;
            }
        }

        return $target;
    }
}
