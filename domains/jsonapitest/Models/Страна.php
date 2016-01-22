<?php
use fja\Model;

class Страна extends Model {        
    public function __construct($id,$attributes,$relationships) {
        $this->primaryKeyName='primarykey';
        $this->attrTypes=[
                'Название'=>'string',
        ];
        parent::__construct($id,$attributes,$relationships);
    }
    
     public static function instance($id=null,$attributes=[],$relationships=[]) {
        return new self($id,$attributes,$relationships);
     }
     
}
