<?php
use fja\Schema;

class SchemaOfМедведь extends Schema
{

    public static $ResourceType='Медведь';
    public static $SelfSubUrl='/Медведи/';
    public static $IsShowSelfInIncluded=true;

    
    public function getIncludePaths() {
        return [];
//        return ['Папа','Мама','ЛесОбитания'];
    }
    
}
