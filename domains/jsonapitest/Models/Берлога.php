<?php
use fja\Model;

class Берлога extends Model {
    public static $PrimaryKeyName='primarykey';
    public static $AttrTypes=[
        'Наименование'=>'string',
        'Комфортность'=>'integer',
        'Заброшена'=>'boolean',
        ];

    public static $relationshipList=[
        'ЛесРасположения'=>'Лес',
        'Медведь'=>'Медведь'
        ];

}
