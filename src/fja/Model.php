<?php
namespace fja;
class Model {
    public $primaryKeyName;
    public $attributes;
    public $relationships;
    protected $attrTypes;
    public static $PrimaryKeyName;
    public static $AttrTypes;
    public static $relationshipList;
    
    public function __construct($id=null,$attributes=[],$relationships=[]) {
        $this->primaryKeyName=static::$PrimaryKeyName;
        $this->attrTypes=static::$AttrTypes;
        $this->attributes=$attributes;
        if ($id) {
            $this->attributes[$this->primaryKeyName]=$id;
        }
        $this->relationships=$relationships;
    }
    
    public static function instance($id=null,$attributes=[],$relationships=[]) {
        return new static($id,$attributes,$relationships);
    }
    
    public function isBoolean($attrName) {
        $attrType=@$this->attrTypes[$attrName];
        $ret=($attrType=='boolean');
        return $ret;        
    }
    
    public function setId($id) {
        $this->attributes[$this->primaryKeyName]=$id;
    }
    
    public static function getAttributeList() {
        $ret=array_keys(static::$AttrTypes);
        return $ret;
    }
    
    public static function getRelationshipList() {
        $ret=array_keys(static::$relationshipList);
        return $ret;
    }
    
    public static function getTypeByRelationName($name) {
        $ret=(key_exists($name,static::$relationshipList)?static::$relationshipList[$name]:'');
        return $ret;
    }

}
