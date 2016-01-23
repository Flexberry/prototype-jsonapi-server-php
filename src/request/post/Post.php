<?php 

namespace request\get;
/*
 *  Class support POST request
 */
class Get {

    /*
     * Get POST data, Decode JSON to Array
     * 
     * @return array
     */
    public static function getPostData() {
        $fp=fopen("php://input",'r');
        $request='';
        while ($str=fgets($fp)) {
            $request.= $str;
        }
        $request=trim($request);
        $ret=json_decode($request,true);
        return $ret;
    }

    public static function decodePostData($postData) {
        if (!key_exists('data',$postData)) {
            sendErrorReply(['status'=>'400','title'=>'Missed field data in request']);
        }
        $ret=[];
        $datas=[];
        $data=$postData['data'];
        if (\fja\FJA::isAssoc($data)) {
            $datas[]=$data;
        } else {
            sendErrorReply(['status'=>'400','title'=>'Several objects in request']);
        }
    //     echo "datas=";print_r($datas);
        foreach ($datas as $data) {
            $ret[]=\fja\FJA::decodeData($data);    
        }
    if (key_exists('included',$postData)) { //Есть включенный объекты
            foreach ($postData['included'] as $subData) {
    //             $ret=array_merge($ret,decodeData($subData));
                $ret[]=fja\FJA::decodeData($subData);
            }
        }
        return $ret;
    }


}