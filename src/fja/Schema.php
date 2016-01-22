<?php
namespace fja;

class Schema extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType;
    protected $selfSubUrl;
    protected $isShowSelfInIncluded;


    public function getId($object) {
        $primaryKeyName=$object->primaryKeyName;
        $ret=(key_exists($primaryKeyName,$object->attributes)?$object->attributes[$primaryKeyName]:null);
        return $ret;
    }

    public function getAttributes($object)
    {
        return $object->attributes;
    }
    
    public function getRelationships($object, array $includeRelationships = []) {
        return $object->relationships;
    }  

    public function getIncludePaths() {
        return [];
    }

        
}
