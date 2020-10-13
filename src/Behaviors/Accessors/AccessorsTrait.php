<?php

namespace ByTIC\DataObjects\Behaviors\Accessors;

use ByTIC\DataObjects\Utility\Constants;
use Nip\Inflector\Inflector;
use Nip\Utility\Str;

/**
 * Trait AccessorsTrait
 * @package ByTIC\DataObjects\Behaviors\Accessors
 */
trait AccessorsTrait
{
    /**
     * The attributes that should use mutators.
     *
     * @var array
     */
    protected static $accessors = [
        'get' => [],
        'set' => [],
    ];

    /**
     * @param $key
     * @return mixed
     */
    public function getMutated($key)
    {
        return $this->callAccessors('get', $key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function setMutated($key, $value)
    {
        return $this->callAccessors('set', $key, [$value]);
    }

    /**
     * @param string $type
     * @param string $key
     * @param array $params
     * @return mixed
     */
    protected function callAccessors(string $type, string $key, $params = [])
    {
        $method = static::getMutator($type, $key);
        if (!$method) {
            return Constants::NO_ACCESSORS_FOUND;
        }
        try {
            return $this->{$method}(...$params);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            if (Str::startsWith($message, 'Undefined property:') && Str::endsWith($message, '::$' . $key)) {
                if ($type === 'get') {
                    return $this->getPropertyRaw($key);
                } elseif ($type === 'set') {
                    return $this->setPropertyRaw($key, $params[0]);
                }
            }
            throw $exception;
        }
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $key
     * @return bool
     */
    protected function hasSetMutator(string $key): bool
    {
        return $this->hasMutator('set', $key);
    }


    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param string $key
     * @return bool
     */
    protected function hasGetMutator(string $key): bool
    {
        return $this->hasMutator('get', $key);
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $type
     * @param string $key
     * @return bool
     */
    protected static function hasMutator(string $type, string $key): bool
    {
        return !empty(static::getMutator($type, $key));
    }

    protected static function getMutator(string $type, string $key): string
    {
        $class = static::class;

        if (empty(static::$accessors[$class])) {
            static::compileMutators();
        }

        if (isset(static::$accessors[$class][$type][$key])) {
            return static::$accessors[$class][$type][$key];
        }

        if (!empty(static::$accessors[$class])) {
            return static::$accessors[$class][$type][$key] = '';
        }

        if (!isset(static::$accessors[$class][$type][$key])) {
            static::$accessors[$class][$type][$key] = '';
        }

        return static::$accessors[$class][$type][$key];
    }

    protected static function compileMutators()
    {
        $class = static::class;

        foreach (get_class_methods($class) as $method) {
            if (in_array($method, ['get','set'])) {
                continue;
            }
            $prefix = substr($method, 0, 3);
            if ($prefix !== 'get' && $prefix !== 'set') {
                continue;
            }

            $field = substr($method, 3);
            if (Str::endsWith($field, 'Attribute')) {
                $field = substr($method, 0, -9);
            }

            static::compileAccessorsMethod($class, $prefix, $method, $field);
        }
    }

    /**
     * @param $class
     * @param $prefix
     * @param $method
     * @param $field
     */
    protected static function compileAccessorsMethod($class, $prefix, $method, $field)
    {
        $field = lcfirst($field);
        static::$accessors[$class][$prefix][$field] = $method;

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $snakeField = Inflector::underscore($field);
        static::$accessors[$class][$prefix][$snakeField] = $method;

        $titleField = ucfirst($field);
        static::$accessors[$class][$prefix][$titleField] = $method;
    }
}
