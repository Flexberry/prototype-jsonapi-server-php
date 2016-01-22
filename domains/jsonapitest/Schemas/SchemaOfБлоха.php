<?php
use fja\Schema;
class SchemaOfБлоха extends Schema {

    function __construct($factory,$container) {
        $this->resourceType = 'Блоха';
        $this->selfSubUrl  = '/Блохи/';
        $this->isShowSelfInIncluded = false;
        parent::__construct($factory,$container);
    }    
}
