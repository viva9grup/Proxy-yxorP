<?php $client = new GuzzleHttp\Client();
$res = $client->get('https://api.github.com/user', ['auth' => ['user', 'pass']]);
echo $res->getStatusCode();
echo $res->getHeader('content-type');
echo $res->getBody();
var_export($res->json());;
return 1; ?><?php return 1; ?>