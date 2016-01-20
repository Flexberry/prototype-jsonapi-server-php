<?php
class SchemaOfЛесОбитания extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'ЛесОбитания';
    protected $selfSubUrl  = '/ЛесаОбитания/';
    protected $isShowSelfInIncluded = true;

    public function getId($лесОбитания) {
        return 0;
    }

    public function getAttributes($лесОбитания)
    {
        return $лесОбитания->attributes;
    }
    
    public function getRelationships($лесОбитания, array $includeRelationships = []) {
        $relationships=$лесОбитания->relationships;
        return $лесОбитания->relationships;
    }  

    public function getIncludePaths() {
        return ['Страна'];
    }
    
}