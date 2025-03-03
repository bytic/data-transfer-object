<?php

declare(strict_types=1);

namespace ByTIC\DataObjects\Tests\Fixtures\Models\Books;

use ByTIC\DataObjects\BaseDto;
use ByTIC\DataObjects\Behaviors\Timestampable\TimestampableTrait;
use ByTIC\DataObjects\Casts\AsArrayObject;
use ByTIC\DataObjects\Casts\Metadata\AsMetadataObject;

/**
 * Class Book.
 */
class Book extends BaseDto
{
    use TimestampableTrait;

    public $timestamps = true;

    protected $name;
    protected $title;

    protected $casts = [
        'published' => 'datetime',
        'metadata' => AsMetadataObject::class . ':json',
        'options' => AsMetadataObject::class . ':json,' . BookOptions::class,
        'properties' => AsArrayObject::class . ':serialize',
    ];

    public static $compiled = 0;

    public static $events = [];

    public function setName(string $name)
    {
        $this->setPropertyValue('name', ucfirst($name));
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTitle()
    {
        return $this->title;
    }

    protected static function compileMutators()
    {
        ++static::$compiled;
        parent::compileMutators();
    }
    public static function creating($callback)
    {
        self::$events['creating'] = $callback;
    }
}
