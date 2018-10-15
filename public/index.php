<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

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
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$app->get('/', function ($request, $response) {
    return $this->renderer->render($response, 'index.phtml');
});

$app->get('/posts', function ($request, $response) use ($repo) {
    $flash = $this->flash->getMessages();

    $params = [
        'flash' => $flash,
        'posts' => $repo->all()
    ];
    return $this->renderer->render($response, 'posts/index.phtml', $params);
})->setName('posts');

// BEGIN (write your solution here)
$app->get('/posts/new', function ($request, $response) use ($repo) {
    return $this->renderer->render($response, 'posts/new.phtml');
});

$app->post('/posts', function ($request, $response) use ($repo) {
    $post = $request->getParsedBodyParam('post');

    $validator = new Validator();
    $errors = $validator->validate($post);

    if (count($errors) === 0) {
        $repo->save($post);
        $this->flash->addMessage('Success', 'Post has been created');
        return $response->withStatus(302)->withRedirect('/posts');
    }

    $params = [
        'post' => $post,
        'errors' => $errors
    ];
    return $this->renderer->render($response, 'posts/new.phtml', $params);
});

$app->get('/posts/{id}/edit', function ($request, $response, $args) use ($repo) {
    $id = $args['id'];
    $post = $repo->find($id);

    $params = ['post' => $post];
    return $this->renderer->render($response, 'posts/edit.phtml', $params);
});

$app->patch('/posts/{id}', function ($request, $response, $args) use ($repo) {
    $id = $args['id'];
    $post = $repo->find($id);
    $updatedData = $request->getParsedBodyParam('post');
    
    $post['name'] = $updatedData['name'];
    $post['body'] = $updatedData['body'];

    $validator = new Validator();
    $errors = $validator->validate($post);

    if (count($errors) === 0) {
        $repo->save($post);
        $this->flash->addMessage('Success', 'Post has been updated');
        return $response->withStatus(302)->withRedirect("/posts");
    }

    $params = [
        'post' => $post,
        'errors' => $errors
    ];
    return $this->renderer->render($response, 'posts/edit.phtml', $params);
});

$app->delete('/posts/{id}', function ($request, $response, $args) use ($repo) {
    $id = $args['id'];
    $repo->destroy($id);
    $this->flash->addMessage('Success', 'Post has been deleted');
    return $response->withStatus(302)->withRedirect('/posts');
});
// END

$app->run();
