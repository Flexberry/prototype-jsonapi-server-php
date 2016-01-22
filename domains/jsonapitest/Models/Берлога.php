<?php
use fja\Model;

class Берлога extends Model {        
    public function __construct($id,$attributes,$relationships) {
        $this->primaryKeyName='primarykey';
        $this->attrTypes=[
        'Наименование'=>'string',
        'Комфортность'=>'integer',
        'Заброшена'=>'boolean',
        ];
        parent::__construct($id,$attributes,$relationships);
    }
    
     public static function instance($id=null,$attributes=[],$relationships=[]) {
        return new self($id,$attributes,$relationships);
     }
     
}
