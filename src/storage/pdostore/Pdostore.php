<?php 
use \responce\Responce;
namespace storage\pdostore;

class Pdostore {

    public static function connectDb() {
        $dsn = 'pgsql:host=10.130.5.119;port=5432;dbname=JsonApiTest;';
        $user = 'flexberry_orm_tester';
        $password = 'sa3dfE';
        try {
            $dbh = new \PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
        }
        return $dbh;
    }

    public static function addObjectToDb($object) {
    //     echo "object=".print_r($object,true);
        $primaryKeyName=$object->primaryKeyName;
    //     echo "primaryKeyName=$primaryKeyName\n";
        if (!key_exists($primaryKeyName,$object->attributes) || !trim($object->attributes[$primaryKeyName])) {
            $primaryKey=\fja\FJA::uuid_gen();
            $object->attributes[$primaryKeyName]=$primaryKey;
        } else {
            $primaryKey=$object->attributes[$primaryKeyName];
        }
        return $object;    
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
        foreach ($object->relationships as $name=>$value) {
            $fieldNames[]='"'.$name.'"';
            $fieldValues[]="'".$value['data']['id']."'";        
        }    
        
        $insertCmd.='('. implode(',',$fieldNames) . ') VALUES (' . implode(',',$fieldValues) . ')';
    //     echo "insertCmd=$insertCmd\n";

        $dbh=self::connectDb();
    //     echo "DBH=".print_r($dbh,true);
        $count = $dbh->exec($insertCmd);
        if (($errorCode=intval($dbh->errorCode()))>0) {
            $errorInfo=$dbh->errorInfo();
            $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
            $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
            $detail="$errorCode/$driverCode: $driverMessage";
            echo $detail;
            \responce\Responce::sendErrorReply(['status'=>'403','title'=>'Object not created, database error','detail'=>$detail]);
        }
    //     echo "Inserted $count records\n";
        return $object;    
    }
    
//     public static function getObject($path,$query) {
//         echo "getObject::path=<pre>";print_r($path);echo "</pre>";
//         echo "getObject::query=<pre>";print_r($query);echo "</pre>";
//         
//     }

    public static function getObjects($modelClassName,$id,$query) {
        if (!$modelClassName || !class_exists($modelClassName)) {
            $detail="The collection ".$path['collection']." does not exist";
            \responce\Responce::sendErrorReply(['status'=>'404','title'=>'The collection does not exist','detail'=>$detail]);            
        }
        $objects=self::selectObjects($modelClassName,$id,$query);
        return $objects;
    }
        
    private static function selectObjects($modelClassName,$id,$query) {
        $objects=[];
        $includePaths=(key_exists('include',$query)?$query['include']:false);
//         echo "selectObjects:: modelClassName=$modelClassName id=$id </pre>";
//         echo "selectObjects::query=<pre>";print_r($query);echo "</pre>";
//         echo "selectObjects::includePaths=<pre>";print_r($includePaths);echo "</pre>";
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
        $fetchtCmd='SELECT ' . implode(',',$FieldList) . " FROM \"public\".\"$modelClassName\""; 
        if ($id!==null) {   //Get Object By Id
            $fetchtCmd.= "WHERE \"". $PrimaryKeyName . "\" = '" . $id . "'";
        }
//         echo "fetchtCmd=$fetchtCmd<br>\n";
        $dbh=self::connectDb();
//         echo "DBH=".print_r($dbh,true);
        $reply = $dbh->query($fetchtCmd);
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
            $relationships=[];
            foreach ($relationshipList as $relationName) {
                $relationId=$row[$relationName];
                if ($relationId) {
                    $relationClassName=$modelClassName::getTypeByRelationName($relationName);
//                     $relationships[$relationName]=['data'=>$relationObject];
                    if (is_array($includePaths)) {
                        if (in_array($relationName,$includePaths)) {
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
                        $relationObjects=self::selectObjects($relationClassName,$relationId,$query);
                        $relationObject=$relationObjects[0];
//                         echo "$relationClassName/$relationId relationObject=";print_r($relationObject);
                    } else {
                        $relationObject=new $relationClassName($relationId);
                    }
                    $relationships[$relationName]=['data'=>$relationObject,'related'=>$related,'showSelf'=>$showSelf,'showData'=>$showData];
                }
            }
//             echo "relationships=";print_r($relationships);
            $object=new $modelClassName($row[$PrimaryKeyName],$attibutes,$relationships);
            $objects[]=$object;
        }
        return $objects;
    }


    
    
}
