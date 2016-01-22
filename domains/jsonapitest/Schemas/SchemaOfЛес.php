<?php
use fja\Schema;
class SchemaOfЛес extends Schema {

    function __construct($factory,$container) {
        $this->resourceType = 'Лес';
        $this->selfSubUrl  = '/Леса/';
        $this->isShowSelfInIncluded = false;
        parent::__construct($factory,$container);
    }    
}
