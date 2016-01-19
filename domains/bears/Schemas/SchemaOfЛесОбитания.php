<?php
class SchemaOfЛесОбитания extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'ЛесаОбитания';

    public function getId($ЛесОбитания) {
        return 0;
    }

    public function getAttributes($ЛесОбитания)
    {
        return $ЛесОбитания->attributes;
    }
}