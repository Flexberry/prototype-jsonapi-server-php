<?php
use fja\Model;

class Лес extends Model {
    public static $PrimaryKeyName='primarykey';
    public static $AttrTypes=[
        'Название'=>'string',
        'Площадь'=>'integer',
        'Заповедник'=>'boolean',
        'ДатаПоследнегоОсмотра'=>'timestamp'
        ];

    public static $relationshipList=[
        'Страна'=>'Страна'
        ];

    public static $reverseRelationshipsList=[
        'Берлога'=>'Берлоги[]',
        'Медведь'=>'Медведи[]'
        ];
   
}