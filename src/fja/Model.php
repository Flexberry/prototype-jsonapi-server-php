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
    	$attrName=strtolower($attrName);
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
    
    public function getRelationships() {
        return $this->relationships;
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
            if ($relType==$type) {
//                 echo "$relType==$relName\n";
                return $relType;
            }
        }
        return false;
    }

    /*
     * Get Inverse relationship Name for  the specified class
     */
    public static function getInverseRelationshipName($type) {
        $ret=false;
        if (isset(static::$reverseRelationshipsList) && key_exists($type,static::$reverseRelationshipsList)) {
            $ret=static::$reverseRelationshipsList[$type];
         }
        return $ret;
    }
    
    
    public static function getRelationshipName($fullName) {
        $ret=$fullName;
        $suffix=substr($ret,-2);
        if ($suffix=='[]' || $suffix=='()') $ret=substr($ret,0,-2);  //Remove [] if exists
        return $ret;
    }
    
    public static function isRelationArray($fullName) {
        $ret=$fullName;
        $suffix=substr($ret,-2);
        $ret= ($suffix=='[]' || $suffix=='()'); 
        return $ret;
    }
    
    public static function isRelationDetais($fullName) {
        $ret=$fullName;
        $suffix=substr($ret,-2);
        $ret= ($suffix=='()'); 
        return $ret;
    }
    
    
    /*
     * Is Inverse relationship Name array or not
     */
    public static function isMultiRelationship($type) {
       $ret=false;
        if (isset(static::$reverseRelationshipsList) && key_exists($type,static::$reverseRelationshipsList)) {
            $ret=static::$reverseRelationshipsList[$type];
            $suffix=substr($ret,-2);
            if ($suffix=='[]' || $suffix=='()') $ret=true;
        }
        return $ret;    
    }

    /*
     * Get keyed list of Reverse Relationships $ReverseRelationships=>true,
     */
    public static function getReverseRelationshipsList() {
        $ret=[];
        if (isset(static::$reverseRelationshipsList)) {
            foreach (static::$reverseRelationshipsList as $reverseRelationshipType=>$reverseRelationshipName) {
                $suffix=substr($reverseRelationshipName,-2);
             if ($suffix=='[]' || $suffix=='()') $reverseRelationshipName=substr($reverseRelationshipName,0,-2);
             $ret[$reverseRelationshipName]=true;
            }
        }
        return $ret;
    }
   
   
     /*
     *  List Links Names for specified type
     */
    public static function ListLinksOfType($type) {
        $ret=[];
        foreach (static::$relationshipList as $relName=>$relType) {
            if ($relType==$type) $ret[]=$relName;
        }
        if ($inverseRelName=self::getInverseRelationshipName($type))  {
            $ret[]=self::getRelationshipName($inverseRelName);
        }
        return $ret;
    }  

//     /*
//      * Table name for Model
//      */
//     public static function getTableName() {
//     	$className=static::class;
//     	$parts=explode('_',$className);
//     	$ret=array_pop($parts);
//     	return $ret;
//     }
}
