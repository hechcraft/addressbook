<?php

use Psr\Http\Message\ServerRequestInterface;
use Slim\Csrf\Guard;
use Slim\Http\Response;
use Slim\Factory\AppFactory;
use DI\Container;

require 'vendor/autoload.php';
require_once('app/api/dbconnect.php');
require_once('app/api/Validation.php');

$app = AppFactory::create();
$db = new dbconnect();
$validate = new Validation();

$app->get('/users', function (ServerRequestInterface $request, Response $response) use ($db) {
    $result = $db->getAllUsers();

    while ($row = $result->fetchArray()) {
        $data[] = $row;
    }

    json_encode($data);

    return $response->withJson($data, 200);
});

$app->get('/users/{id}', function (ServerRequestInterface $request, Response $response) use ($db) {
    $userId = $request->getAttribute('id');
    $result = $db->getUsers($userId);
    if ($result) {
        json_encode($result);
        return $response->withJson($result, 200);
    }

    return $response->withStatus(404);
});

$app->post('/users', function (ServerRequestInterface $request, Response $response) use ($db, $validate) {
    $uri = $request->getUri()->getQuery();

    parse_str($uri, $postParams);

    if ($validate->validate($postParams['name'], $postParams['address'], $postParams['phone'])) {
        echo 'Error validation';
        return $response->withStatus(404);
    }

    $result = $db->saveUser($postParams['name'], $postParams['address'], $postParams['phone']);

    if ($result) {
        echo 'Successfully save new user';
        return $response->withStatus(200);
    }
    return $response->withStatus(404);
});

$app->put('/users/{id}', function (ServerRequestInterface $request, Response $response) use ($db, $validate) {
    $uri = $request->getUri()->getQuery();
    parse_str($uri, $postParams);

    if ($validate->validate($postParams['name'], $postParams['address'], $postParams['phone'])) {
        echo 'Error validation';
        return $response->withStatus(404);
    }

    $userId = $request->getAttribute('id');
    $result = $db->updateUser($userId, $postParams['name'], $postParams['address'], $postParams['phone']);

    if ($result) {
        echo 'Successfully update';
        return $response->withStatus(200);
    }
    return $response->withStatus(404);
});

$app->delete('/users/{id}', function (ServerRequestInterface $request, Response $response) use ($db) {
    $userId = $request->getAttribute('id');

    $result = $db->userDelete($userId);
    if ($result) {
        echo 'Entry deleted successfully';
        return $response->withStatus(200);
    }

    return $response->withStatus(404);
});

$app->get('/search', function (ServerRequestInterface $request, Response $response) use ($db) {
    $uri = $request->getUri()->getQuery();
    parse_str($uri, $postParams);


    $result = $db->search($postParams['searchColumn'], $postParams['searchRequest']);

    if (empty($result->fetchArray())){
        echo 'Not found';
        return $response->withStatus(404);
    }

    while ($row = $result->fetchArray()) {
        $data[] = $row;
    }

    json_encode($data);

    return $response->withJson($data, 200);
});
$app->run();