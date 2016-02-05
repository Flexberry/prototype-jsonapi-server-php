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

    /*
     * Set relationships  in included Objects
     * $included - array included objects
     * $className - className Objects involded included objects
     */

    public static function setRelationsInIncludedObjects($included,$containerObject=false) {
        if ($containerObject && is_object($containerObject)) {
            $classType=get_class($containerObject);
            $primaryKey=$containerObject->getId();
        }
        $includePaths=[];
        $included=\fja\FJA::includedToObjectsArray($included);
        echo "AddObject::INCLUDED=";print_r($included);
        foreach ($included as $id=>$includeObject) {
            $includedPrimaryKey=\fja\FJA::uuid_gen();   //Primary key generation
            $includeObject->setId($includedPrimaryKey);
        }        
        foreach ($included as $id=>$includeObject) {
            $includeObjectType=get_class($includeObject);
            if ($containerObject) {
                $LinkRelName=$includeObject->getRelationNameByType($classType); //Get Relation Name of included object, that points to $classType
                if ($LinkRelName) {
                    $includeObject->setRelationship($LinkRelName,$classType,$primaryKey);
                    echo "setRelationship($includeObjectType -> $LinkRelName,$classType,$primaryKey)\n";
                }
                $includePaths=array_merge($includePaths,$containerObject->ListLinksOfType($includeObjectType));
            }
            echo "includeObjectType=$includeObjectType includePaths=";print_r($includePaths);
            if (isset($includeObject->relationships) && is_array($includeObject->relationships))  { //Set inverse Relationsships in referenced included objects
                foreach ($includeObject->relationships as $relName=>$relDesc) {
                    echo "setRelationsInIncludedObjects::relName=$relName $relDesc=";print_r($relDesc);
                    if (!is_array($relDesc) || !key_exists('data',$relDesc)) continue; 
                    if (!is_array($relDesc['data'])) continue;
                    if (key_exists('type',$relDesc['data'])) {
                        $type=$relDesc['data']['type'];
                    } else continue;
                    if (key_exists('id',$relDesc['data'])) {
                        $id=$relDesc['data']['id'];
                    } else continue;
                    if (key_exists($id,$included)) {    //In inlude tHere exists objec, that referenced by this 
                        $referencedObject=$included[$id];
                        $includeObject->setRelationship($relName,$type,$referencedObject->getId());              
                        $invercedRelName=$referencedObject->getInverseRelationshipName($includeObjectType);
                        echo "invercedRelName=$invercedRelName referencedObject=";print_r($referencedObject);
                        if ($invercedRelName) {
                            $invRelname=\fja\Model::getRelationshipName($invercedRelName);
                            $data=['type'=>$includeObjectType,'id'=>$includeObject->getId()];
                            if (\fja\Model::isRelationArray($invercedRelName)) {
                                if (!isset($referencedObject->relationships[$invRelname]) || !key_exists('data',$referencedObject->relationships[$invRelname])) {
                                    $referencedObject->relationships[$invRelname]['data']=[];
                                }
                                $referencedObject->relationships[$invRelname]['data'][]=$data;
                            } else {
                                $referencedObject->relationships[$invRelname]['data']=$data;
                            }
                        }
                    }
                }
            }
//                 Pdostore::addObjectToDb($includeObject);
        }
        return [$included,$includePaths];
    }


}