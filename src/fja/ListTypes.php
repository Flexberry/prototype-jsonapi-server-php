<?php 
namespace fja;

class ListTypes {

    /* Get type of Class by subUrl 
     * @param string subUrl - url for collection retrive
     * 
     * @return type of class
     */
    public static function getTypeBySubUrl($subUrl) {
        $subUrl=trim($subUrl,'/');
        $subUrl=str_replace('-','_',$subUrl);
        foreach (static::$listTypes as $type) {
            $schemaClassName='SchemaOf'.$type;
            $schemaSubUrl=isset($schemaClassName::$SelfSubUrl)?trim($schemaClassName::$SelfSubUrl,'/'):'';
//             echo "type=$type $subUrl==$schemaSubUrl<br>\n";
            if ($subUrl==$schemaSubUrl) {
//                 echo "RET=$type<br>\n";
                return $type;
            }
        }
        return false;
    }
    
    
}

