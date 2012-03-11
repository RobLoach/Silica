<?php

require_once __DIR__ . '/../vendor/.composer/autoload.php';

// Create the new Application.
$app = new Silica\Application();

$app['debug'] = TRUE;

$app->get('/', function () use ($app) {
    return $app['twig']->render('default.twig', array(
        'title' => 'Welcome',
        'content' => 'hows it goin',
        'navigation' => array(
            'ssdf' => array(
                 'href' => 'hello/motherfucker',
                 'title' => 'your mom',
            ),
        )
    ));
})
->bind('homepage');

$app->get('/hello/{name}', function ($name) {
    return "Hello $name!";
})
->bind('hello');

$app->run();
