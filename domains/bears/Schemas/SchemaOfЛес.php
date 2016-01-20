<?php
class SchemaOfЛес extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Лес';
    protected $selfSubUrl  = '/Леса/';
    protected $isShowSelfInIncluded = true;

    public function getId($лес) {
        return $лес->attributes['primarykey'];
    }

    public function getAttributes($лес)
    {
        return $лес->attributes;
    }
    
    public function getRelationships($лес, array $includeRelationships = []) {
        $relationships=$лес->relationships;
        return $лес->relationships;
    }  

    public function getIncludePaths() {
        return ['Страна'];
    }
    
}