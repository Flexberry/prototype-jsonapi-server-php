<?php
// use \Neomerx\JsonApi\Encoder;
require(__DIR__ . '/../../vendor/autoload.php');

class BearSchema extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'Медведи';

    public function getId($медведь) {
        return $медведь->attributes['ПорядковыйНомер'];
    }

    public function getAttributes($медведь)
    {
        return $медведь->attributes;
    }
    
    public function getRelationships($медведь, array $includeRelationships = []) {
        return $медведь->relationships;
    }  
}

class ForestSchema extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'ЛесаОбитания';

    public function getId($ЛесОбитания) {
        return 0;
    }

    public function getAttributes($ЛесОбитания)
    {
        return $ЛесОбитания->attributes;
    }
}


class ЛесОбитания {
    public $attributes;
    public $relationships;
    
    function __construct($attributes=[],$relationships=[]) {
        $this->attributes=$attributes;
        $this->relationships=$relationships;
    }
}

class Медведь {
    public $attributes;
    public $relationships;
    
    function __construct($attributes=[],$relationships=[]) {
        $this->attributes=$attributes;
        $this->relationships=$relationships;
        $this->id=$attributes['ПорядковыйНомер'];
    }
}

$ЛесОбитания1=new ЛесОбитания(
    [
        'Название'=>'Беловежская Пуща',
        'Площадь'=>222,
        'Заповедник'=>false,
        'ДатаПоследнегоОсмотра'=>'2010/05/05'
    ]
    );

$медведь1= new \Медведь(
    [ 
        'ПорядковыйНомер'=>1,
        'Вес'=>101,
        'ЦветГлаз'=>'Зеленый',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2010/06/08'
    ],
    [
        'ЛесОбитания' => ['data' => $ЛесОбитания1],
    ]
    );

$медведь2= new \Медведь(
    [ 
        'ПорядковыйНомер'=>2,
        'Вес'=>96,
        'ЦветГлаз'=>'Карий',
        'Пол'=>'Женский',
        'ДатаРождения'=>'2010/09/15'
    ],
    [
        'ЛесОбитания' => ['data' => $ЛесОбитания1],
    ]
    );

$медведь3= new \Медведь(
    [ 
        'ПорядковыйНомер'=>3,
        'Вес'=>65,
        'ЦветГлаз'=>'Синий',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2012/12/15'
    ],
    [
        'ЛесОбитания' => ['data' => $ЛесОбитания1],
        'Папа' => ['data' =>  $медведь1 ],
        'Мама' => ['data' =>  $медведь2 ],
    ]
    );

    
echo "медведь3=";print_r($медведь3);

$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'Медведь' => '\BearSchema',
    'ЛесОбитания' => '\ForestSchema',
], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, 'http://flexberryJSONAPI.nevod.ru/v1'));

$json=$encoder->encodeData($медведь3);

$phpData=json_decode($json,true);

echo "<pre>JSON=$json</pre>\n";
echo "<pre>PHP=";print_r($phpData);echo "</pre>\n";


