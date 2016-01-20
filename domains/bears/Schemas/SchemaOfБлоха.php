<?php
class SchemaOfБлоха extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Блоха';
    protected $selfSubUrl  = '/Блохи/';
    protected $isShowSelfInIncluded = true;

    public function getId($блоха) {
        return $блоха->attributes['primarykey'];
    }

    public function getAttributes($блоха)
    {
        return $блоха->attributes;
    }
    
    public function getRelationships($блоха, array $includeRelationships = []) {
        $relationships=$блоха->relationships;
        return $блоха->relationships;
    }  
    
}