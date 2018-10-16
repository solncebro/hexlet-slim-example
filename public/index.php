<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function Stringy\create as s;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

session_start();

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$users = [
    ['name' => 'admin', 'passwordDigest' => hash('sha256', 'secret')],
    ['name' => 'mike', 'passwordDigest' => hash('sha256', 'superpass')],
    ['name' => 'kate', 'passwordDigest' => hash('sha256', 'strongpass')],
    ['name' => '123', 'passwordDigest' => hash('sha256', '123')]
];

// BEGIN (write your solution here)
$app->get('/', function ($request, $response) {
    $flash = $this->flash->getMessages();

    $params = [
        'currentUser' => $_SESSION['user'] ?? null,
        'flash' => $flash
        ];
    return $this->renderer->render($response, '/index.phtml', $params);
});

$app->post('/session', function ($request, $response) use ($users) {
    $formData = $request->getParsedBodyParam('user');
    $loginUser = ['name' => $formData['name'], 'passwordDigest' => hash('sha256', $formData['password'])];

    $foundUser = array_filter($users, function ($user) use ($loginUser) {
        if ($user == $loginUser) {
            return true;
        }
        return false;
    });

    if (empty($foundUser)) {
        $this->flash->addMessage('Wrong', 'Wrong password or name');
    } else {
        $_SESSION['user'] = $formData;
    }
    
    return $response->withRedirect('/');
});

$app->delete('/session', function ($request, $response) {
    session_unset();
    session_destroy();
    return $response->withRedirect('/');
});
// END

$app->run();
