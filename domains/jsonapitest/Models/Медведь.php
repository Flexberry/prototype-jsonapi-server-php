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
    
    public static $reverseRelationshipsList=[
        'Берлога'=>'Берлоги()',
        'Папа'=>'Дети[]',
        'Мама'=>'Дети[]',
        'Блоха'=>'Блохи[]'
        ];
}
