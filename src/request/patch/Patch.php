<?php 
namespace request\patch;
use \fja\FJA;
use \storage\pdostore\Pdostore;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;
use \Neomerx\JsonApi\Schema\Link;
/*
 *  Class support PATCH request
 */
class Patch extends \request\Request {

    /*
     * update the specified in the PATH and Body object 
     * 
     * @return string or false
     */
     public static function updateObject($path,$baseURL) {
        $json=false;
        $id=$path['id'];
        $object=Patch::dataToObject(Patch::getBody());
        $succes=Pdostore::updateObject($object,$id); 
        return $json;
    }

 
}