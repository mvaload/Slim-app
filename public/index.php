<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function Stringy\create as s;

$users = Generator::generate(100);

$repo = new Repository();

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');

$app->get('/', function ($request, $response) {
    return $this->renderer->render($response, 'index.phtml');
});

$app->get('/users', function ($request, $response) use ($users) {
    $page = $request->getQueryParam('page', 1);
    $per = $request->getQueryParam('per', 5);
    $offset = ($page - 1) * $per;

    $sliceOfUsers = array_slice($users, $offset, $per);
    $params = [
        'users' => $sliceOfUsers,
        'page' => $page
    ];
    return $this->renderer->render($response, 'users/index.phtml', $params);
});

$app->get('/courses/new', function ($request, $response) use ($repo) {
    $params = [
        'course' => [],
        'errors' => []
    ];
    return $this->renderer->render($response, 'courses/new.phtml', $params);
});

$app->post('/courses', function ($request, $response) use ($repo) {
    $course = $request->getParsedBodyParam('course');

    $validator = new Validator();
    $errors = $validator->validate($course);

    if (count($errors) === 0) {
        $repo->save($course);
        return $response->withRedirect('/courses');
    }

    $params = [
        'course' => $course,
        'errors' => $errors
    ];

    return $this->renderer->render($response, 'courses/new.phtml', $params);
});

$app->get('/users/{id}', function ($request, $response, $args) use ($users) {
    $id = (int) $args['id'];
    $user = collect($users)->first(function ($user) use ($id) {
        return $user['id'] == $id;
    });
    $params = ['user' => $user];
    return $this->renderer->render($response, 'users/show.phtml', $params);
});

$app->get('/companies/{id}', function ($request, $response, $args) use ($companies) {
    $id = $args['id'];
    $company = collect($companies)->firstWhere('id', $id);
    if (!$company) {
        return $response->withStatus(404)->write('Page not found');
    }
    return $response->write(json_encode($company));
});

$app->run();
