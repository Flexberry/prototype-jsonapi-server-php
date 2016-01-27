<?php
namespace fja;

class Schema extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    public static $ResourceType;
    public static $SelfSubUrl;
    public static $IsShowSelfInIncluded;

    function __construct($factory,$container) {
        $this->resourceType = static::$ResourceType;
        $this->selfSubUrl  = static::$SelfSubUrl;
        $this->isShowAttributesInIncluded = static::$IsShowSelfInIncluded;
        echo "Schema=";print_r($this);
        parent::__construct($factory,$container);
    }    


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
