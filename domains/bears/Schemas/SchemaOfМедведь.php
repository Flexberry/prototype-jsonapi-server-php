<?php
class SchemaOfМедведь extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Медведи';

    public function getId($медведь) {
        return $медведь->attributes['ПорядковыйНомер'];
    }

    public function getAttributes($медведь)
    {
        return $медведь->attributes;
    }
    
    public function getRelationships($медведь, array $includeRelationships = []) {
        return $медведь->relationships;
    }  
}
