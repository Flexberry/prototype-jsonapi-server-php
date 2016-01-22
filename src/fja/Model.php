<?php
namespace fja;
class Model {
    public $primaryKeyName;
    public $attributes;
    public $relationships;
    protected $attrTypes;
    
    public function __construct($id=null,$attributes=[],$relationships=[]) {
        if ($id) {
            $attributes[$this->primaryKeyName]=$id;
        }
        $this->attributes=$attributes;
        $this->relationships=$relationships;
    }
    

    public function isBoolean($attrName) {
        $attrType=@$this->attrTypes[$attrName];
        $ret=($attrType=='boolean');
        return $ret;        
    }
    
    public function setId($id) {
        $this->attributes[$this->primaryKeyName]=$id;
    }

}
