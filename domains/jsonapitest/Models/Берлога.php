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
  
    public function __construct($id=null,$attributes=[],$relationships=[]) {
        if ($id) {
            $attributes[$this->primaryKeyName]=$id;
        }
        $this->attributes=$attributes;
        $this->relationships=$relationships;
    }
    
     public static function instance($id=null,$attributes=[],$relationships=[]) {
        return new self($id,$attributes,$relationships);
     }
     
    public function isBoolean($attrName) {
        $attrType=@$this->attrTypes[$attrName];
        $ret=($attrType=='boolean');
        return $ret;        
    }

    
}