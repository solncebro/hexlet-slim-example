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

$app->get('/', function ($request, $response) {
    $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);
    $params = [
        'cart' => $cart
    ];
    return $this->renderer->render($response, 'index.phtml', $params);
});

// BEGIN (write your solution here)
$app->post('/cart-items', function ($request, $response) {
    $addItem = $request->getParsedBodyParam('item');
    $id = $addItem['id'];

    $cart = json_decode($request->getCookieParam('cart', json_encode([])), true);

    if (!isset($cart[$id])) {
        $cart[$id] = ['name' => $addItem['name'], 'count' => 1];
    } else {
        $cart[$id]['count'] += 1;
    }

    $encodedCart = json_encode($cart);

    return $response->withHeader('Set-Cookie', "cart={$encodedCart}")->withRedirect('/');
});

$app->delete('/cart-items', function ($request, $response) {
    $encodedCart = json_encode([]);
    return $response->withHeader('Set-Cookie', "cart={$encodedCart}")->withRedirect('/');
});
// END

$app->run();
