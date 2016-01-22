<?php
use fja\Schema;
class SchemaOfБерлога extends Schema {

    function __construct($factory,$container) {
        $this->resourceType = 'Берлога';
        $this->selfSubUrl  = '/Берлоги/';
        $this->isShowSelfInIncluded = false;
        parent::__construct($factory,$container);
    }    
}
