<?php

namespace ByTIC\DataObjects\Casts;

/**
 * Interface CastsAttributes
 * @package ByTIC\DataObjects\Casts
 */
interface CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param object $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes);

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param object $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes);
}
