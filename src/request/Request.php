<?php 
namespace request;
/*
 *  Class support POST request
 */
class Request {

    /*
     * Get POST/PATCH/DELETE data, Decode JSON to Array
     * 
     * @return array
     */
    public static function getBody() {
        $fp=fopen("php://input",'r');
        $request='';
        while ($str=fgets($fp)) {
            $request.= $str;
        }
        $request=trim($request);
        $ret=json_decode($request,true);
        return $ret;
    }

    public static function dataToObject($postData) {
        if (!key_exists('data',$postData)) {
            sendErrorReply(['status'=>'400','title'=>'Missed field data in request']);
        }
        $data=$postData['data'];
        if (!is_array($data)) {
            sendErrorReply(['status'=>'400','title'=>'Incorrect data  in request']);
        }
        if (!\fja\FJA::isAssoc($data)) {
            sendErrorReply(['status'=>'400','title'=>'Several objects in request']);
        } 
    //     echo "datas=";print_r($datas);
        $ret=\fja\FJA::dataToObject($data);    
        return $ret;
    }


}