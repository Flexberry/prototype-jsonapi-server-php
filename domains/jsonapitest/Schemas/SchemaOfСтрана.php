<?php
use fja\Schema;
class SchemaOfСтрана extends Schema {

    function __construct($factory,$container) {
        $this->resourceType = 'Страна';
        $this->selfSubUrl  = '/Страны/';
        $this->isShowSelfInIncluded = false;
        parent::__construct($factory,$container);
    }    
}
