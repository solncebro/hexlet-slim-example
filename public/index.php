<?php

namespace App;

require 'vendor/autoload.php';

use function Stringy\create as s;

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

$app->get('/courses', function ($request, $response) use ($repo) {
    $params = [
        'courses' => $repo->all()
    ];
    return $this->renderer->render($response, 'courses/index.phtml', $params);
});

// BEGIN (write your solution here)
$app->get('/courses/new', function ($request, $response) use ($repo) {
    $params = [
        'courses' => $repo->all()
    ];
    return $this->renderer->render($response, 'courses/new.phtml', $params);
});

$app->post('/courses', function ($request, $response) use ($repo) {
    $validator = new Validator();
    $course = $request->getParsedBodyParam('course');
    $errors = $validator->validate($course);

    if (empty($errors)) {
        $repo->save($course);
        return $response->withRedirect('/');
    }

    $params = [
        'course' => $course,
        'errors' => $errors
    ];

    return $this->renderer->render($response, 'courses/new.phtml', $params);
});
// END

$app->run();
