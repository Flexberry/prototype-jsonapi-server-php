<?php
class SchemaOfБерлога extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Берлога';
    protected $selfSubUrl  = '/Берлоги/';
    protected $isShowSelfInIncluded = true;

    public function getId($берлога) {
        return $берлога->attributes['primarykey'];
    }

    public function getAttributes($берлога)
    {
        return $берлога->attributes;
    }
    
    public function getRelationships($берлога, array $includeRelationships = []) {
        $relationships=$берлога->relationships;
        return $берлога->relationships;
    }  
    
}