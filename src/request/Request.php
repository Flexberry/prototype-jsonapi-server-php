<?php 
namespace request;
use \responce\Responce;
/*
 *  Class support POST request
 */
class Request {

    /*
     * Get Body of REST request
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

    static public function urlParse($request_uri) {
        while (substr($request_uri,0,1)=='/') $request_uri=substr($request_uri,1);
        $parsed=parse_url($request_uri);
        $location=trim(urldecode($parsed['path']),'/');   
        $query=key_exists('query',$parsed)?urldecode($parsed['query']):'';  
        while (($nPath=str_replace('//','/',$location))!=$location) $location=$nPath;   //replace // to / in path
        $steps=explode('/',$location);
        $retPath['collection']=$steps[0];
        $type=\ListTypes::getTypeBySubUrl($retPath['collection']);
        if (!$type) {
            Responce::sendErrorReply(['status'=>'400','title'=>"Unknown collection ". $retPath['collection']]);    
        }
        $retPath['type']=$type;
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
        
        if (key_exists('sort',$Query)) {
            $sort=[];
            foreach (explode(',',$Query['sort']) as $sortField) {
                if (substr($sortField,0,1)=='-') {
                    $sort[]=['field'=>substr($sortField,1),'asc'=>false];
                } else {
                    $sort[]=['field'=>$sortField,'asc'=>true];
                }
            }
            $retQuery['sort']=$sort;
        }
        if (key_exists('page',$Query)) {
            $retQuery['page']=$Query['page'];
        }
        if (key_exists('filter',$Query)) {
            $retQuery['filter']=$Query['filter'];
        }
        $ret=['path'=>$retPath,'query'=>$retQuery,'location'=>"/$location"];
        return $ret;       
    }

    
    public static function includedToObjectsArray($postData) {
        $ret=[];
        if (key_exists('included',$postData)) {
            $ret=\fja\FJA::includedToObjectsArray($postData['included']);
        }
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