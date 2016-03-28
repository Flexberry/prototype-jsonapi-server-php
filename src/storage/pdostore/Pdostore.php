<?php 
use \responce\Responce;
use \request\get\Get;

namespace storage\pdostore;

class Pdostore {

	static $database='flexberry-ember-demo';
	static $selectedObjects=[];
    static $includePaths=[];
    static $sort;
    static $page;
    static $total;
    
    public static function setDomain($domain) {
    	self:$database=str_replace('-','_',$domain);
    }

    public static function connectDb() {
//         $dsn = 'pgsql:host=10.130.5.119;port=5432;dbname=JsonApiTest;';
//         $user = 'flexberry_orm_tester';
//        $password = 'sa3dfE';
        $dsn = 'pgsql:host=127.0.0.1;port=5432;dbname='. self:$database .';';
        $user = 'demo';
        $password = 'demo';
        try {
            $dbh = new \PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
        }
        return $dbh;
    }

    public static function updateObject($object,$id) {
//          echo "object=".print_r($object,true);
        $className=get_class($object);
        $PrimaryKeyName=$className::$PrimaryKeyName;
        $updateCmd="UPDATE $className SET ";
        $set=[];
        foreach ($object->attributes as $name=>$value) {
            if ($name==$PrimaryKeyName) continue;
            $set[]="$name = '$value'";
        }
       foreach ($object->relationships as $name=>$descr) {
            $value=$descr['data']['id'];
            $set[]="$name = '$value'";
        }
        $updateCmd.= implode(', ',$set);
        $updateCmd.=" WHERE $PrimaryKeyName = '$id'";
//         echo "updateCmd=$updateCmd\n";
       $dbh=self::connectDb();
    //     echo "DBH=".print_r($dbh,true);
        $count = $dbh->exec($updateCmd);
        if (($errorCode=intval($dbh->errorCode()))>0) {
            $errorInfo=$dbh->errorInfo();
            $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
            $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
            $detail="$errorCode/$driverCode: $driverMessage";
            echo $detail;
            \responce\Responce::sendErrorReply(['status'=>'403','title'=>'Object not updated, database error','detail'=>$detail]);
        }
        $ret=true;
        return $ret;
    }

    public static function addObjectToDb($object) {
    //     echo "object=".print_r($object,true);
//         return $object;    
    //     echo "primaryKey=$primaryKey\n";
        $className=get_class($object);
        $insertCmd="INSERT INTO public." . $className . ' ';; 
        $fieldNames=[];
        $fieldValues=[];
        foreach ($object->attributes as $name=>$value) {
            $fieldNames[]='"'.$name.'"';
            if ($object->isBoolean($name)) {
                $value=($value?'true':'false');
            } else {
                $value="'".$value."'";
            }
            $fieldValues[]=$value;
        }
        $ReverseRelationshipsList=$className::getReverseRelationshipsList();
//         echo "Relationships==";print_r($object->relationships);
//         echo "ReverseRelationshipsList==";print_r($ReverseRelationshipsList);
        foreach ($object->relationships as $name=>$value) {
            if (key_exists($name,$ReverseRelationshipsList)) continue;
            $id=$value['data']['id'];
            $fieldNames[]='"'.$name.'"';
            $fieldValues[]=($id?"'$id'":'NULL');        
        }    
        
        $insertCmd.='('. implode(',',$fieldNames) . ') VALUES (' . implode(',',$fieldValues) . ')';
//         echo "insertCmd=$insertCmd\n";

        $dbh=self::connectDb();
    //     echo "DBH=".print_r($dbh,true);
        $count = $dbh->exec($insertCmd);
        if (($errorCode=intval($dbh->errorCode()))>0) {
            $errorInfo=$dbh->errorInfo();
            $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
            $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
            $detail="$errorCode/$driverCode: $driverMessage";
//             echo $detail;
            \responce\Responce::sendErrorReply(['status'=>'403','title'=>'Object not created, database error','detail'=>$detail]);
        }
    //     echo "Inserted $count records\n";
        return $object;    
    }
    
    public static function deleteRelationship($modelClassName,$id,$relationship,$body) {
//         echo "Change (delete parts of) relationship $relationship in object $modelClassName: $collection/$id\n";
//         echo "BODY=";print_r($body);
        if ($body) {
            if (!key_exists('data',$body)) {
                \responce\Responce::sendErrorReply(['status'=>'400','title'=>"Body request does'nt contain data field"]);
            }
            elseif ($body['data']!='null') {
                \responce\Responce::sendErrorReply(['status'=>'400','title'=>'Request data field contain not null, unsupported in current release']);
            }
        }
        $PrimaryKeyName=$modelClassName::$PrimaryKeyName;
        $dbh=self::connectDb();
        $tableName=$modelClassName::getTableName();
        $updateCmd="UPDATE $tableName SET $relationship = null WHERE $PrimaryKeyName = '$id'";
//         echo "updateCmd=$updateCmd\n";
        $reply = $dbh->query($updateCmd);
        $ErrorCode=$dbh->errorCode();
        $errorCode=intval($ErrorCode);
//         echo "ERRORCODE=$errorCode<br>\n";
        if ($errorCode>0) {
            switch ($ErrorCode) {  
                default:
                    $errorInfo=$dbh->errorInfo();
                    $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
                    $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
                    $detail="$errorCode/$driverCode: $driverMessage";
//                     echo "$errorCode=". $dbh->errorCode() . " errorInfo=". print_r($errorInfo,true) . "<br>\n";
                    \responce\Responce::sendErrorReply(['status'=>'404','title'=>'Objects are not available','detail'=>$detail]);
            }
        }
    }

    public static function deleteObject($modelClassName,$id) {
//         echo "Delete object  $collection/$id\n";
        $PrimaryKeyName=$modelClassName::$PrimaryKeyName;
        $dbh=self::connectDb();
        $tableName=$modelClassName::getTableName();
        $deleteCmd="DELETE FROM $tableName WHERE $PrimaryKeyName = '$id'";
        echo "deleteCmd=$deleteCmd\n";
        $reply = $dbh->query($deleteCmd);
        $ErrorCode=$dbh->errorCode();
        $errorCode=intval($ErrorCode);
        if ($errorCode>0) {
            switch ($ErrorCode) {  
                default:
                    $errorInfo=$dbh->errorInfo();
                    $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
                    $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
                    $detail="$errorCode/$driverCode: $driverMessage";
//                     echo "$errorCode=". $dbh->errorCode() . " errorInfo=". print_r($errorInfo,true) . "<br>\n";
                    \responce\Responce::sendErrorReply(['status'=>'404','title'=>'Objects are not available','detail'=>$detail]);
            }
        }
        $count=$reply->rowCount();
//         echo "ERRORCODE=$errorCode count=$count reply=\n";print_r($reply);
        if ($count==0) {
            \responce\Responce::sendErrorReply(['status'=>'404','title'=>"Removed object with id=$id does'nt  exist"]);
        }
        
    }
    

    public static function getObjects($modelClassName,$id,$query) {
        if (!$modelClassName || !class_exists($modelClassName)) {
            $detail="The collection ".$path['collection']." does not exist";
            \responce\Responce::sendErrorReply(['status'=>'404','title'=>'The collection does not exist','detail'=>$detail]);            
        }
        self::$includePaths=(key_exists('include',$query)?$query['include']:false);
        self::$sort=(key_exists('sort',$query)?$query['sort']:false);
        self::$page=(key_exists('page',$query)?$query['page']:false);
        $objects=self::selectObjects($modelClassName,$id,'');
        return $objects;
    }
        
    private static function selectObjects($modelClassName,$id,$relNamePath) {
        $dbh=self::connectDb();
        $objects=[];
//         echo "\n\n---------------\nselectObjects:: modelClassName=$modelClassName id=$id  relNamePath=$relNamePath\n";
        $PrimaryKeyName=$modelClassName::$PrimaryKeyName;
        $fieldList=[$PrimaryKeyName];
//         echo "type=";print_r($modelClassName);
        $attributeList=$modelClassName::getAttributeList();
//         echo "attributeList=";print_r($attributeList);
        $relationshipList=$modelClassName::getRelationshipList();
//         echo "relationshipList=";print_r($relationshipList);
        $fieldList=array_merge($fieldList,$attributeList,$relationshipList);
//         echo "fieldList=";print_r($fieldList);
        $FieldList=[];
        foreach ($fieldList as $fieldName) {
            $FieldList[]='"' . $fieldName . '"';
        }
        $tableName=$modelClassName::getTableName();
        $fetchCmd='SELECT ' . implode(',',$FieldList) . " FROM \"public\".\"$tableName\""; 
        if ($id!==null) {   //Get Object By Id
            $fetchCmd.= " WHERE \"". $PrimaryKeyName . "\" = '" . $id . "'";
        }
//         echo "SORT=";print_r(self::$sort);
//         echo "lenOf relNamePath=".strlen($relNamePath)."\n";
        if (is_array(self::$sort) && strlen($relNamePath)==0) { //Sorting on top level
            $Sort=[];
            foreach (self::$sort as $fieldDesc) {
                $Sort[]=$fieldDesc['field'] . ' ' . ($fieldDesc['asc']?'ASC':'DESC');
            }
            $fetchCmd.=" ORDER BY " .  implode(',',$Sort);
        }
        if (is_array(self::$page) && strlen($relNamePath)==0) { //Paging on top level
            $countCmd="SELECT COUNT(*) AS total FROM ($fetchCmd) AS n";
            $reply = $dbh->query($countCmd);
            foreach ($reply as $row) {
//                 echo "COUNTROW=";print_r($row);echo "<br>\n";
                self::$total=$row['total'];
                break;
            }

            if (key_exists('number',self::$page)) {
                $fetchCmd .= " OFFSET " . self::$page['number'];
            }
           if (key_exists('size',self::$page)) {
                $fetchCmd .= " LIMIT " . self::$page['size'];
            }
        }
//         echo "fetchtCmd=$fetchCmd<br>\n";
//         echo "DBH=".print_r($dbh,true);
        $reply = $dbh->query($fetchCmd);
        $ErrorCode=$dbh->errorCode();
        $errorCode=intval($ErrorCode);
//         echo "ERRORCODE=$errorCode<br>\n";
        if ($errorCode>0) {
            switch ($ErrorCode) {  
                case '22P02':   //Неверный синтаксис UUID
                    return [];  //Корректная ситуация 
                break;
                default:
                    $errorInfo=$dbh->errorInfo();
                    $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
                    $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
                    $detail="$errorCode/$driverCode: $driverMessage";
//                     echo "$errorCode=". $dbh->errorCode() . " errorInfo=". print_r($errorInfo,true) . "<br>\n";
                    \responce\Responce::sendErrorReply(['status'=>'403','title'=>'Objects are not available','detail'=>$detail]);
            }
        }
        foreach ($reply as $row) {
//             echo "ROW=";print_r($row);echo "<br>\n";
            $attibutes=[];
            foreach ($attributeList as $attributeName) {
                $attibutes[$attributeName]=$row[$attributeName];
            }
            $id=$row[$PrimaryKeyName];
//             echo "\nmodelClassName=$modelClassName id=$id selectedObjects=";print_r(self::$selectedObjects);echo "\n";
            if (key_exists($modelClassName,self::$selectedObjects) && key_exists($id,self::$selectedObjects[$modelClassName])) {
//                 echo "Object $modelClassName/$id exist\n";
                $object=self::$selectedObjects[$modelClassName][$id];
            } else {
                $object=new $modelClassName($id,$attibutes,[]);
                self::$selectedObjects[$modelClassName][$id]=$object;
//                 echo "After adding: selectedObjects=";print_r(self::$selectedObjects);echo "\n";
                $relationships=[];
                foreach ($relationshipList as $relationName) {
                    $relationId=$row[$relationName];
                    if ($relationId) {
                        $relationClassName=$modelClassName::getTypeByRelationName($relationName);
                        if (is_array(self::$includePaths)) {
                            $fullRelationName="$relNamePath$relationName";
//                             echo "fullRelationName=$fullRelationName\n";
                            if (in_array($fullRelationName,self::$includePaths)) {
                                $related=true;
                                $showSelf=true;
                                $showData=true;
                                } else {
                                $related=true;
                                $showSelf=true;
                                $showData=false;
                            }
                        } else {
                            $related=true;
                            $showSelf=true;
                            $showData=false;
                        }
                        if ($showData) {
                            if (key_exists($relationClassName,self::$selectedObjects) && key_exists($relationId,self::$selectedObjects[$relationClassName])) {
//                                 echo "RelationObject $relationClassName/$relationId exist\n";
                                $relationObject=self::$selectedObjects[$relationClassName][$relationId];
                            } else {
                                $subRelNamePath=($relNamePath?"$relNamePath$relationName.":"$relationName.");
                                $relationObjects=self::selectObjects($relationClassName,$relationId,$subRelNamePath);
                                $relationObject=$relationObjects[0];
                                self::$selectedObjects[$relationClassName][$relationId]=$relationObject;
        //                         echo "$relationClassName/$relationId relationObject=";print_r($relationObject);
                            }
                        } else {
                            $relationObject=new $relationClassName($relationId);
                        }
                        $relationships[$relationName]=['data'=>$relationObject,'related'=>$related,'showSelf'=>$showSelf,'showData'=>$showData];
                    }
                }
    //             echo "relationships=";print_r($relationships);
                $object->setRelationships($relationships);
//             $object=new $modelClassName($row[$PrimaryKeyName],$attibutes,$relationships);
            }
            $objects[]=$object;
        }
        return $objects;
    }
    
}
