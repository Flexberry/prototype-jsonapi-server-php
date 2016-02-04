<?php 
namespace request\patch;
/*
 *  Class support PATCH request
 */
class Patch extends \request\Request {

    /*
     * update the specified in the PATH and Body object 
     * 
     * @return string or false
     */
     public static function updateObject($path) {
        $json=false;
        $id=$path['id'];
        $object=Patch::dataToObject(Patch::getBody());
        $succes=Pdostore::updateObject($object,$id); 
        return $json;
    }

 
}