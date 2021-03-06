<?php

use eCampApi\ConfigFactory;

$entity = 'ContentTypeConfigTemplate';
$config = ConfigFactory::createConfig($entity);

// read-only endpoint
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['entity_http_methods'] = ['GET'];
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['collection_http_methods'] = ['GET'];

array_push(
    $config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['collection_query_whitelist'],
    'activityCategoryTemplateId'
);

return $config;
