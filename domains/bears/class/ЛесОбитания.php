<?php
class ЛесОбитания extends stdClass {
    public $attributes;
    public $relationships;
    
    function __construct($attributes=[],$relationships=[]) {
        $this->attributes=$attributes;
        $this->relationships=$relationships;
    }
}