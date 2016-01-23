<?php 

namespace request\get;
/*
 *  Class support GET request
 */
class Get {
    
    static public function urlParse($request_uri) {
        while (substr($request_uri,0,1)=='/') $request_uri=substr($request_uri,1);
        $parsed=parse_url($request_uri);
        $path=trim(urldecode($parsed['path']),'/');   
        $query=urldecode($parsed['query']);  
        while (($nPath=str_replace('//','/',$path))!=$path) $path=$nPath;   //replace // to / in path
        $steps=explode('/',$path);
        $retPath['collection']=$steps[0];
        if (count($steps)>1) {
            $retPath['id']=$steps[1];
            if (count($steps)>2) {
                if ($steps[2]=='relationships') {
                    $retPath['relationship']=@$steps[3];
                } else {
                    $retPath['attribute']=$steps[2];
                }
            }
            
        }
        parse_str($query,$retQuery);
        $ret=['path'=>$retPath,'query'=>$retQuery];
        return $ret;
        
    }

}