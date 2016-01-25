<?php
use fja\Model;

class Медведь extends Model {
    public static $PrimaryKeyName='primarykey';
    public static $AttrTypes=[
        'ПорядковыйНомер'=>'integer',
        'Вес'=>'integer',
        'ЦветГлаз'=>'string',
        'Пол'=>'string',
        'ДатаРождения'=>'timestamp'
        ];

    public static $relationshipList=[
        'Папа'=>'Медведь',
        'Мама'=>'Медведь',
        'ЛесОбитания'=>'Лес'
        ];

//     public function __construct($id,$attributes,$relationships) {
//         $this->primaryKeyName=static::$PrimaryKeyName;
//         $this->attrTypes=static::$AttrTypes;
//         parent::__construct($id,$attributes,$relationships);
//     }
    
//     public static function instance($id=null,$attributes=[],$relationships=[]) {
//         return new static($id,$attributes,$relationships);
//     }
     
}