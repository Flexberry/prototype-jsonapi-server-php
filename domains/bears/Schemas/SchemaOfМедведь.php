<?php
class SchemaOfМедведь extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Медведь';
    protected $selfSubUrl  = '/Медведи/';
    protected $isShowSelfInIncluded = true;


    public function getId($медведь) {
        return $медведь->attributes['ПорядковыйНомер'];
    }

    public function getAttributes($медведь)
    {
        return $медведь->attributes;
    }
    
    public function getRelationships($медведь, array $includeRelationships = []) {
        $relationships=$медведь->relationships;
        return $медведь->relationships;
    }  
    
    public function getIncludePaths() {
        return ['ЛесОбитания','Папа','Мама'];
    }
    
}
