<?php
// use \Neomerx\JsonApi\Encoder;
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../fja/FJA.php');  //Базовый класс Flexberry JSON API

spl_autoload_register(['\fja\FJA', 'autoload'], true, true);


\fja\FJA::autoload('Models/Медведь');
\fja\FJA::autoload('Schemas/SchemaOfМедведь');

\fja\FJA::autoload('Models/ЛесОбитания');
\fja\FJA::autoload('Schemas/SchemaOfЛесОбитания');


$fp=fopen("php://input",'r');

$request='';

while ($str=fgets($fp)) {
    $request.= $str;
}
$request=trim($request);
print_r(json_decode($request,true));
// echo "REQUEST='$request'\n";

