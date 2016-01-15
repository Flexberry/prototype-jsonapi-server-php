#!/bin/sh

baseURL="http://flexberryJsonAPI.local/";

curl --request POST \ 
    $baseURL/Медведи \
    -H 'Content-Type: application/vnd.api+json' \
    -H 'Accept: application/vnd.api+json' \    
    -d '
{
    "data": {
        "type": "Медведь",
        "attributes": {
            "title": "Ember Hamster",
            "src": "http://example.com/images/productivity.png"
        },
        "relationships": {
            "photographer": {
                "data": { "type": "people", "id": "9" }
            }
        }
    }
}'