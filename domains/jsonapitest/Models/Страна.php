<?php
use fja\Model;

class Страна extends Model {
    public static $PrimaryKeyName='primarykey';
    public static $AttrTypes=[
        'Название'=>'string',
        ];

    public static $relationshipList=[
        ];
        
    public static $backRelationshipsList=[
        'Леса'=>'Лес[]',
        ];
    
}
