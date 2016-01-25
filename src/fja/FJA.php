<?php
namespace fja;
require_once(__DIR__ . '/UUID.php');

// $domain=dirName($_SERVER["REQUEST_URI"]);
// echo "DOMAIN=$domain\n";
// echo "FJA::" . __DIR__."\n";
class FJA {

    public static $domainsDir;
    public static $domain;
    private static $defaultDomain='jsonapitest';    //Домен по умолчвнию (в случае осутствия указанного)
    private static $domainToDBName= [
        'jsonapitest'=>'JsonApiTest'
        ];
    private static $domainIncludeDir;
    private static $schemasIncludeDir;
    private static $modelsIncludeDir;
    private static $FJAIncludeDir;
    
    public static function setDomainsDir($domainsDir) {
        self::$domainsDir=$domainsDir;
//         echo "domainsDir=". self::$domainsDir . "\n" ;
    }

    public static function setDomain($domain) {
        if (!is_dir(self::$domainsDir . "/$domain")) {
            $domain=self::$defaultDomain;
        }
        self::$domain=$domain;
        self::$domainIncludeDir=self::$domainsDir . "/" . self::$domain;
        self::$schemasIncludeDir=self::$domainIncludeDir."/Schemas";
        self::$modelsIncludeDir=self::$domainIncludeDir."/Models";
        self::$FJAIncludeDir= __DIR__."/..";
    }

    public static function autoload($className) {
//         echo "AUTOLOAD $className:\n";
         $path=explode('\\',trim($className,'\\'));  
//         echo "PATH=".print_r($path,true);
        if (count($path)==1) {  // JSONAPI Class
            $className=$path[0];
            $classFile="$className.php";
            $found=false;
            if ($className=='ListTypes') {
                $listTypesFile=self::$domainIncludeDir."/ListTypes.php";
                include_once($listTypesFile);
            } elseif (substr($className,0,8)=='SchemaOf') {   //Subclass of fja\Schema
                $schemaClassFile=self::$schemasIncludeDir."/$classFile";
//                 echo "schemaClassFile=$schemaClassFile\n";
                if (file_exists($schemaClassFile)) {
//                     echo "SchemaClass $className Found\n\n";
                    $found=true;
                    include_once($schemaClassFile);
                }
            }
            if (!$found) {  // Subclass of fja\Model
                $modelClassFile=self::$modelsIncludeDir."/$classFile";
//                 echo "modelClassFile='$modelClassFile'\n";
                if (file_exists($modelClassFile)) {
//                     echo "ModelClass $className Found\n\n";
                    include_once($modelClassFile);
                }
            }
        } else  { //if ($path[0]=='fja') {    //Class of fja\... namespace
            $classFile=str_replace('\\',"/",$className) . ".php";
            $FJAClassFile=self::$FJAIncludeDir."/$classFile";
//             echo "classFile=$classFile FJAClassFile=$FJAClassFile\n";
            if (file_exists($FJAClassFile)) {
//                 echo "FJAClass $className Found\n\n";
                include_once($FJAClassFile);
            }            
        } 
    }
    
    public static function getDataFromJson($json) {
        $jsonTree=json_decode($json,true);
        return $jsonTree['data'];
    }

    public static function decodeData($data) {
        if (!key_exists('type',$data)) {
            echo "Отсутствует аттрибут type в " . print_r($data,true) ;
            return [];
        }
        $type=$data['type'];
        if (!key_exists('attributes',$data)) {
            echo "Отсутствует аттрибут attributes в " . print_r($data,true) ;
            return [];
        }
        $attributes=$data['attributes'];
        $relationships=[];
        if (key_exists('relationships',$data)) {
            $relationships=$data['relationships'];
        }
        
        $modelClass="Models/$type";
    //     echo "modelClass=$modelClass\n";
        \fja\FJA::autoload($modelClass);
        $schemaClass="Schemas/SchemaOf$type";
        \fja\FJA::autoload($schemaClass);
    //     echo "schemaClass=$schemaClass\n";
        $className="\\$type";
    //     echo "className=$className\n";
        $object=new $className(null,$attributes,$relationships);
    //     echo "Object=".print_r($object,true);
        return $object; 
    }
    
    public static function getDBName() {
        $ret=(key_exists(self::$domain,self::$domainToDBName)?self::$domainToDBName[self::$domain]:self::$domain);
        return $ret;
    }    
    
    public static function uuid_gen() {
        $ret=\UUID::v4();
        return $ret;
    }
    
    public static function isAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    
    public static function formSchemas($objects) {
        $ret=[];
        foreach ($objects as $object) {
            $type=get_class($object);
            $ret[$type]="SchemaOf$type";
            if (isset($object->relationships) && is_Array($object->relationships)) {
                foreach ($object->relationships as $relationships) {
                    if (key_exists('data',$relationships) && key_exists('type',$relationships['data'])) {
                        $subType=$relationships['data']['type'];
                        $ret[$subType]="SchemaOf$subType";
                    }
                }
            }
        }
        return $ret;
    }

    
    
    /*
    * Replace ['type']=>type,['id']->id on object in relationships
    */
    public static function replaceRelationshipsObject($object) {
        if (isset($object->relationships) && is_Array($object->relationships)) {
            foreach ($object->relationships as $relName=>$relationships) {
                if (key_exists('data',$relationships) && key_exists('id',$relationships['data'])) {
                    $subType=$relationships['data']['type'];
                    $subId=$relationships['data']['id'];
    //                 echo "subType=$subType subId=$subId\n";
                    $modelClass="Models/$subType";
                    \fja\FJA::autoload($modelClass);
                    $schemaClass="Schemas/SchemaOf$subType";
                    \fja\FJA::autoload($schemaClass);
                    $object->relationships[$relName]['data']=new $subType($subId);
                }
            }
        }
        return $object;
    }
    
}