<?php
class Берлога extends stdClass {
    public $primaryKeyName='primarykey';
    public $attributes;
    public $relationships;
    private $attrTypes=[
        'Наименование'=>'string',
        'Комфортность'=>'integer',
        'Заброшена'=>'boolean',
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