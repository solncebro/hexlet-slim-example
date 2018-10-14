<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// BEGIN (write your solution here)
$app->get('/', function ($request, $response) {
    $messages = $this->flash->getMessages();
    $params = ['flash' => $messages];
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->post('/courses', function ($request, $response) {
    $this->flash->addMessage('Success', 'Course Added');
    
    return $response->withStatus(302)->withHeader('Location', '/');
});
// END

$app->run();
