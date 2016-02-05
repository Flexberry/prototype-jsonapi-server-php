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
    
    public function getId() {
        $ret=$this->attributes[$this->primaryKeyName];
        return $ret;
    }
    
    public function setRelationships($relationships) {
        $this->relationships=$relationships;
    }
    
    public function setRelationship($relationName,$relationType,$relationValue) {
        $data=['type'=>$relationType,'id'=>$relationValue];
        $object=new $relationType($relationValue);
        $this->relationships[$relationName]['data']=$data;
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
        if (substr($ret,-2)=='[]') {
            $ret=substr($ret,0,-2);
        }
        return $ret;
    }
    
    public static function getRelationNameByType($type) {
        foreach (static::$relationshipList as $relName=>$relType) {
            if ($relType==$relName) {
                return $relType;
            }
        }
        return '';
    }

    /*
     * Get Inverse relationship Name for  the specified class
     */
    public static function getInverseRelationshipName($relName) {
        $ret=false;
        if (isset(static::$reverseRelationshipsList) && key_exists($relName,static::$reverseRelationshipsList)) {
            $ret=static::$reverseRelationshipsList[$relName];
            if (substr($ret,-2)=='[]') $ret=substr($ret,0,-2);  //Remove [] if exists
        }
        return $ret;
    }
    
    /*
     * Is Inverse relationship Name array or not
     */
    public static function isMultiRelationship($relName) {
       $ret=false;
        if (isset(static::$reverseRelationshipsList) && key_exists($relName,static::$reverseRelationshipsList)) {
            $ret=static::$reverseRelationshipsList[$relName];
            if (substr($ret,-2)=='[]') $ret=true;
        }
        return $ret;    
    }
   

}
