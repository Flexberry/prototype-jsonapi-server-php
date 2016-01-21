<?php
class SchemaOfЛес extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Лес';
    protected $selfSubUrl  = '/Леса/';
    protected $isShowSelfInIncluded = false;

    public function getId($лес) {
        $ret=(key_exists('primarykey',$лес->attributes)?$лес->attributes['primarykey']:null);
        return $ret;
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
        return [];
//        return ['Страна'];
    }
    
}