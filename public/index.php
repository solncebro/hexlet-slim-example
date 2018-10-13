<?php

namespace App;

require 'vendor/autoload.php';

$users = Generator::generate(100);

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

// BEGIN (write your solution here)
$app->get('/users', function ($request, $response) use ($users) {
    $page = $request->getQueryParam('page', 1);
    $per = $request->getQueryParam('per', 5);
    $offset = ($page - 1) * $per;
    $maxPages = (int) round(sizeof($users) / $per, 0, PHP_ROUND_HALF_UP);
    $sliceUsers = array_slice($users, $offset, $per);

    $params = [
        'usersArray' => $sliceUsers,
        'page' => $page,
        'maxPages' => $maxPages
        ];
    return $this->renderer->render($response, 'users/index.phtml', $params);
});

$app->get('/users/{id}', function ($request, $response, $args) use ($users) {
    $id = (int) $args['id'];
    $user = collect($users)->first(function ($value, $key) use ($id) {
        return $value['id'] === $id;
    });
    $params = ['user' => $user];
    return $this->renderer->render($response, 'users/show.phtml', $params);
});

$app->run();
// END
