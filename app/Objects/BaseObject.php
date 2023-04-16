<?php

namespace App\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use RoachPHP\ItemPipeline\AbstractItem;

abstract class BaseObject extends AbstractItem implements Arrayable, Jsonable, JsonSerializable
{
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    public static function make(array $properties = []): self
    {
        return new static($properties);
    }

    protected function setProperties(array $properties): void
    {
        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __get(string $key): mixed
    {
        return $this->{$key};
    }

    public function __set(string $key, mixed $value): void
    {
        $this->{$key} = $value;
    }

    public function toArray(): array
    {
        return $this->all();
    }

    public function toJson($flags = 0): string
    {
        return json_encode($this->all(), $flags);
    }

    public function jsonSerialize(): array
    {
        return $this->all();
    }
}
