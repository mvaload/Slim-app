<?php

require __DIR__ . '/../vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$app->get('/', function ($request, $response) {
    $response->write('Welcome to Slim!');
    return $response;
});

$app->get('/users', function ($request, $response) {
    $response->write('GET /users');
    return $response;
});

$app->post('/users', function ($request, $response) {
    $response->write('POST /users');
    return $response;
});

$app->run();
