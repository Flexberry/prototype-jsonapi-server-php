<?php
namespace fja;
// $domain=dirName($_SERVER["REQUEST_URI"]);
// echo "DOMAIN=$domain\n";

class FJA {

    public static $domainsDir;
    public static $domain;
    private static $domainToDBName= [
        'jsonapitest'=>'JsonApiTest'
        ];
    
    public static function setDomainsDir($domainsDir) {
        self::$domainsDir=$domainsDir;
//         echo "domainsDir=". self::$domainsDir . "\n" ;
    }

    public static function setDomain($domain) {
        self::$domain=$domain;
//         echo "domain=". self::$domain . "\n" ;
    }

    public static function autoload($className)
    {
        if (@is_array($_SERVER) && key_exists('DOCUMENT_ROOT',$_SERVER) && key_exists('HTTP_HOST',$_SERVER)) {  //Called in WEB-environment
            self::setDomainsDir($_SERVER["DOCUMENT_ROOT"]. "/../../domains");
            $path=explode('.',trim($_SERVER["HTTP_HOST"],'/'));
//             echo "PATH=";print_r($path);exit;
            $domain=$path[0];
            self::setDomain($domain);
        }
        $includeDir=self::$domainsDir . "/" . self::$domain;
//         echo "domainsDir=". self::$domainsDir . " domain=" . self::$domain  . " includeDir=$includeDir\n" ;
        ini_set('include_path', $includeDir. ":" . ini_get('include_path'));
        $classFile=str_replace('\'',"/",$className) . ".php";
        $ClassFile="$includeDir/$classFile";
//         echo "classFile=$classFile ClassFile=$ClassFile\n";
        if (file_exists($ClassFile)) {
            include_once($classFile);
        }
    }
    
    public static function getDBName() {
        $ret=(key_exists(self::$domain,self::$domainToDBName)?self::$domainToDBName[self::$domain]:self::$domain);
        return $ret;
    }    

}