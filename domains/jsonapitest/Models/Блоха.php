<?php
use fja\Model;

class Блоха extends Model {
    public static $PrimaryKeyName='primarykey';
    public static $AttrTypes=[
        'Кличка'=>'string',
        ];

    public static $relationshipList=[
        'МедведьОбитания'=>'Медведь'
        ];
   
     
}
