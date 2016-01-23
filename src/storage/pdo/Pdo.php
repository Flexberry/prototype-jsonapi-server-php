<?php 
use \responce\Responce;
namespace storage\pdo;

class Pdo {

    public static function connectDb() {
        $dsn = 'pgsql:host=10.130.5.119;port=5432;dbname=JsonApiTest;';
        $user = 'flexberry_orm_tester';
        $password = 'sa3dfE';
        try {
            $dbh = new PDO($dsn, $user, $password);
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

        $dbh=Pdo::connectDb();
    //     echo "DBH=".print_r($dbh,true);
        $count = $dbh->exec($insertCmd);
        if (($errorCode=intval($dbh->errorCode()))>0) {
            $errorInfo=$dbh->errorInfo();
            $driverCode=(key_exists(1,$errorInfo)?$errorInfo[1]:'');
            $driverMessage=(key_exists(2,$errorInfo)?$errorInfo[2]:'');
            $detail="$errorCode/$driverCode: $driverMessage";
            echo $detail;
            Responce::sendErrorReply(['status'=>'403','title'=>'Object not created, database error','detail'=>$detail]);
        }
    //     echo "Inserted $count records\n";
        return $object;    
    }

}
