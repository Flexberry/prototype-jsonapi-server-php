<?php 
namespace responce;

class Responce {

    public static function  sendCreatedObject($location,$json,$headers=[]) {
        $status='201';
        http_response_code($status);
        $headers[]="Content-type: application/vnd.api+json";
        $headers[]="Location: $location";
        foreach ($headers as $header) {
            header($header);
        }
        echo $json;
    }


    public static function sendErrorReply($e,$headers=[]) {
        $status=$e['status'];
        $status='200';
        http_response_code($status);
        $headers[]="Content-type: application/vnd.api+json";
        foreach ($headers as $header) {
            header($header);
        }
        $url="http://".$_SERVER["SERVER_NAME"]. $_SERVER["REQUEST_URI"];
        $encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $url));
        $error=new \Neomerx\JsonApi\Document\Error(@$e['idx'],@$e['aboutLink'],@$e['status'],@$e['code'],@$e['title'],@$e['detail'],@$e['source'],@$e['meta']);
        $body=$encoder->encodeError($error);
        echo $body;
        exit;
    }
}