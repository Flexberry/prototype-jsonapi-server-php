<?php
use GuzzleHttp\Exception\ClientException;

$oDataServer="http://flexberry-ember-demo.azurewebsites.net/odata";

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../src/fja/FJA.php');

$baseHost='http://jsonapiserver.local';
$domain="flexberry-ember-demo";
$baseURL="$baseHost/$domain";
\fja\FJA::setDomainsDir(__DIR__."/../../domains");
\fja\FJA::setDomain($domain);
spl_autoload_register(['\fja\FJA', 'autoload'], true, true);

$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'flexberry_ember_demo_suggestion_type' => '\SchemaOfflexberry_ember_demo_suggestion_type',

], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));


$odataRestClient = new GuzzleHttp\Client(['base_uri' => $oDataServer]);
$restClient = new GuzzleHttp\Client(['base_uri' => $baseURL]);


$suggestionTypesURL="$oDataServer/FlexberryEmberDemoSuggestionTypes";

try {
	$reply=$odataRestClient->request('GET',$suggestionTypesURL);
} catch (ClientException $e) {
		echo "Ошибка в выполнении запроса: ";
	if ($e->hasResponse()) {
		$response=$e->getResponse();
		echo "StatusCode=".$response->getStatusCode()."\n";
		$body=$response->getBody();
		$jsonPos=strpos($body,'{');
		echo "Carbage=".substr($body,0,$jsonPos);;
		$content=json_decode(strstr($body,'{'),true);
		echo "\nShift=$jsonPos\nContent=";print_r($content);
	}
	exit;
}
$body=$reply->getBody();
echo "BODY=$body";
$odataBody=json_decode(strstr($body,'{'),true);
$suggestionTypesList=$odataBody['value'];

foreach ($suggestionTypesList as $odataSuggestionType) {
	print_r($odataSuggestionType);
	$suggestionType= flexberry_ember_demo_suggestion_type::instance(
		$odataSuggestionType['__PrimaryKey'],
		[
			'Name'=>$odataSuggestionType['Name'],
			'Moderated'=>$odataSuggestionType['Moderated'],
		]
		);
	print_r($suggestionType);
	$reply=sendPOSTRequest($restClient,$encoder,"flexberry_ember_demo_suggestion_type","$domain/flexberry_ember_demo_suggestion_types",$suggestionType);

}


function sendPOSTRequest($restClient,$encoder,$title,$uri,$instance) {
    $body=$encoder->encodeData($instance);
    $str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');}, $body);
    echo "Demo::Sent:\n$str\n";
    try {
        $reply=$restClient->request('POST',$uri, ['body'=>$body]);
    } catch (ClientException $e) {
        echo "Ошибка в выполнении запроса: ";
        if ($e->hasResponse()) {
            $response=$e->getResponse();
            echo "StatusCode=".$response->getStatusCode()."\n";
            $body=$response->getBody();
            $jsonPos=strpos($body,'{');
            echo "Carbage=".substr($body,0,$jsonPos);;
            $content=json_decode(strstr($body,'{'),true);
            echo "\nShift=$jsonPos\nContent=";print_r($content);
        }
        exit;
    }
    echo "\n\n---------------- $title -------------\n";
    echo "Demo::StatusCode=" . $reply->getStatusCode() . "\n";
//     echo "Demo::Headers="; print_r($reply->getHeaders());
    $body=$reply->getBody();
    echo "Demo::Body=$body\n";
    echo "Demo::BODY=".print_r(json_decode($body,true),true);
    return $body;
}