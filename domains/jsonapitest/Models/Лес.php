<?php
use fja\Model;

class Лес extends Model {        
    public function __construct($id,$attributes,$relationships) {
        $this->primaryKeyName='primarykey';
        $this->attrTypes=[
        'Название'=>'string',
        'Площадь'=>'integer',
        'Заповедник'=>'boolean',
        'ДатаПоследнегоОсмотра'=>'timestamp'
        ];
        parent::__construct($id,$attributes,$relationships);
    }
    
     public static function instance($id=null,$attributes=[],$relationships=[]) {
        return new self($id,$attributes,$relationships);
     }
     
}