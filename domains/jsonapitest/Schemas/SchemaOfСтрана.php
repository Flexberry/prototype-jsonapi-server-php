<?php
class SchemaOfСтрана extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Страна';
    protected $selfSubUrl  = '/Страны/';
    protected $isShowSelfInIncluded = false;


    public function getId($страна) {
        $ret=(key_exists('primarykey',$страна->attributes)?$страна->attributes['primarykey']:null);
        return $ret;
    }

    public function getAttributes($страна)
    {
        return $страна->attributes;
    }
    
        
}
