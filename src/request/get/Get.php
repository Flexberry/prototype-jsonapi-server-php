<?php 

namespace request\get;
/*
 *  Class support GET request
 */
class Get {
    
    static public function urlParse($request_uri) {
        while (substr($request_uri,0,1)=='/') $request_uri=substr($request_uri,1);
        $parsed=parse_url($request_uri);
        $location=trim(urldecode($parsed['path']),'/');   
        $query=key_exists('query',$parsed)?urldecode($parsed['query']):'';  
        while (($nPath=str_replace('//','/',$location))!=$location) $location=$nPath;   //replace // to / in path
        $steps=explode('/',$location);
        $retPath['collection']=$steps[0];
        if (count($steps)>1) {
            $retPath['id']=$steps[1];
            if (count($steps)>2) {
                if ($steps[2]=='relationships') {
                    $retPath['relationship']=@$steps[3];
                } else {
                    $retPath['related']=array_slice($steps,2);
                }
            }
            
        }
        parse_str($query,$Query);
//         echo "Query=";print_r($Query);
        $retQuery=[];
        if (key_exists('include',$Query)) {
//             echo "Before Include=".$Query['include']."\n";
            $include=[];
            foreach (explode(',',$Query['include']) as $includePath) { //Form full list: [...,'comments.author',...] -> [...,'comments','comments.author',...]
                $dotPaths=explode('.',$includePath);
                $subIncludePath=[];
                foreach ($dotPaths as $dotPath) {   
                    $subIncludePath[]=$dotPath;
                    $include[implode('.',$subIncludePath)]=true;
                }
            }
            $include=array_keys($include);
            $retQuery['include']=$include;
//             echo "After Include=";print_r($include);
        }
        $fields=[];
        if (key_exists('fields',$Query) && is_array($Query['fields'])) {
            foreach ($Query['fields'] as $type=>$fieldList) {
                $fields[$type]=explode(',',$fieldList);
            }
        }
        $retQuery['fields']=$fields;
        $ret=['path'=>$retPath,'query'=>$retQuery,'location'=>"/$location"];
        return $ret;       
    }

}