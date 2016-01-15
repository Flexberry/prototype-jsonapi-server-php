<?php
// use \Neomerx\JsonApi\Encoder;
require(__DIR__ . '/../vendor/autoload.php');

class AuthorSchema extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'people';

    public function getId($author)
    {
        /** @var Author $author */
        return $author->authorId;
    }

    public function getAttributes($author)
    {
        /** @var Author $author */
        return [
            'firstName' => $author->firstName,
            'lastName'  => $author->lastName,
        ];
    }
}


class Author {
    public $firstName;
    public $lastName;
    
    function __construct($firstName,$lastName) {
        $this->firstName=$firstName;
        $this->lastName=$lastName;
        $this->authorId="$firstName@$lastName";
    }
}

$author= new \Author('Alexey','Kostarev');
$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'Author' => '\AuthorSchema',
], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, 'http://flexberryJSONAPI.nevod.ru/v1'));

echo $encoder->encodeData($author) . PHP_EOL;