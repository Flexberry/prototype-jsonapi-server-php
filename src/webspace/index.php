<?php
// use \Neomerx\JsonApi\Encoder;
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../fja/FJA.php');  //Базовый класс Flexberry JSON API

spl_autoload_register(['\fja\FJA', 'autoload'], true, true);


\fja\FJA::autoload('class/Медведь');
\fja\FJA::autoload('schema/SchemaOfМедведь');

\fja\FJA::autoload('class/ЛесОбитания');
\fja\FJA::autoload('schema/SchemaOfЛесОбитания');


$fp=fopen("php://input",'r');

$request='';

while ($str=fgets($fp)) {
    $request.= $str;
}
$request=trim($request);
echo "REQUEST='$request'\n";

