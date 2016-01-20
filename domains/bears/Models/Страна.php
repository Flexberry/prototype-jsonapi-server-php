<?php

class Страна extends stdClass {

    public $attributes;
    public $relationships;
    
    public function __construct($attributes=[],$relationships=[]) {
        $this->attributes=$attributes;
        $this->relationships=$relationships;
    }
    
     public static function instance($attributes=[],$relationships=[]) {
        return new self($attributes,$relationships);
     }
    
}
