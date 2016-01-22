<?php
use fja\Model;

class Медведь extends Model {        
    public function __construct($id,$attributes,$relationships) {
        $this->primaryKeyName='primarykey';
        $this->attrTypes=[
        'ПорядковыйНомер'=>'integer',
        'Вес'=>'integer',
        'ЦветГлаз'=>'string',
        'Пол'=>'string',
        'ДатаРождения'=>'timestamp'
        ];
        parent::__construct($id,$attributes,$relationships);
    }
    
     public static function instance($id=null,$attributes=[],$relationships=[]) {
        return new self($id,$attributes,$relationships);
     }
     
}