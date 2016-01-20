<?php
class SchemaOfСтрана extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Страна';
    protected $selfSubUrl  = '/Страны/';
    protected $isShowSelfInIncluded = true;


    public function getId($страна) {
        return $страна->attributes['primarykey'];
    }

    public function getAttributes($страна)
    {
        return $страна->attributes;
    }
    
        
}
