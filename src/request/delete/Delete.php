<?php 
use \request\Request;
namespace request\delete;
/*
 *  Class support DELETE request
 */
class Delete extends \request\Request {

    /*
     * Remove the specified in the PATH object 
     * 
     * @return string or false
     */
     public static function deleteObject($path) {
        $id=$path['id'];
        $json=false;
        if (key_exists('relationship',$path) && trim($path['relationship'])) {
            $relationship=$path['relationship'];
            $body=Delete::getBody();
//             echo "postData=";print_r($postData);
            Pdostore::deleteRelationship($path['type'],$id,$relationship,$body); 
        } else {
            Pdostore::deleteObject($path['type'],$id);             
        }
        return $json;
    }
 
}