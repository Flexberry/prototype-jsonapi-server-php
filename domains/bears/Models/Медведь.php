<?php

class Медведь extends stdClass {
    public $attributes;
    public $relationships;
    
    function __construct($attributes=[],$relationships=[]) {
        $this->attributes=$attributes;
        $this->relationships=$relationships;
        $this->id=$attributes['ПорядковыйНомер'];
    }
}
