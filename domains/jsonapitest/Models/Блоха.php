<?php
class Блоха extends stdClass {
    public $primaryKeyName='primarykey';
    public $attributes;
    public $relationships;
    private $attrTypes=[
        'Кличка'=>'string',
        ];
    
    function __construct($attributes=[],$relationships=[]) {
        $this->attributes=$attributes;
        $this->relationships=$relationships;
    }
    
    public static function instance($attributes=[],$relationships=[]) {
        return new self($attributes,$relationships);
     }
     
    public function isBoolean($attrName) {
        $attrType=@$this->attrTypes[$attrName];
        $ret=($attrType=='boolean');
        return $ret;        
    }
    
}