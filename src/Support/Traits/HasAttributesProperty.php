<?php

namespace PulsarLabs\Generators\Support\Traits;

trait HasAttributesProperty
{
    protected string $attributes_prefix = 'protected $attributes = [';
    protected string $attributes_suffix = "\t];";
    protected string $attributes = '';

    public function getAttributesProperty(array $columns): string
    {
        //        return $this->attributes_prefix . "\n" . $this->attributes . $this->attributes_suffix;
        return "";
    }
}
