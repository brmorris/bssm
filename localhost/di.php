<?php

  use Phalcon\DI\FactoryDefault,
  Phalcon\Assets\Manager as AssetsManager,
  Phalcon\Mvc\View\Simple as View,
  Phalcon\Mvc\View\Engine\Volt,
  Phalcon\Mvc\Collection\Manager as CollectionManager,
  Phalcon\Session\Adapter\Files as Session;
  use Phalcon\Mvc\Collection;

$di = new FactoryDefault();

// $di['assets'] = function() {
//   $assets = new AssetsManager();
//   $assets
//     ->addJs('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', false)
//     ->addJs('//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js', false);
//   return $assets;
// };

//View service

$di['view'] = function() {
  $view = new View();
//  echo "BRAD: Setting APP_PATH to " . APP_PATH . '/views/';
  $view->setViewsDir(APP_PATH . '/views/');
  $view->registerEngines(array(
    '.volt' => function ($view, $di) {
      $volt = new Volt($view, $di);
      $volt->setOptions(array(
        'compiledPath' => APP_PATH . '/cache/',
        'compiledSeparator' => '_'
      ));
      return $volt;
    }
  ));

  return $view;
};

// /**
//  * Flash service with custom CSS classes
//  */
// $di['flash'] = function(){
//   return new Flash(array(
//     'error' => 'alert alert-error',
//     'success' => 'alert alert-success',
//     'notice' => 'alert alert-info',
//   ));
// };
//
// $di['flashDirect'] = function(){
//   return new FlashDirect(array(
//     'error' => 'alert alert-error',
//     'success' => 'alert alert-success',
//     'notice' => 'alert alert-info',
//   ));
// };

$di['session'] = function(){
  $session = new Session(array(
    'uniqueId' => 'dasshy-'
  ));
  $session->start();
  return $session;
};

// // Simple database connection to localhost
// $di['mongo'] = function() {
//     $mongo = new Mongo(); $m = new MongoDB\Driver\Manager("mongodb://localhost:27017");
//     return $mongo->selectDb("services");
// };

// $di->set('config', function () {
//     return new \Phalcon\Config([
//         'mongodb' => [
//             'host' => 'localhost',
//             'port' => 27017,
//             'database' => 'auto'
//         ]
//     ]);
// }, true);

// $di->set('mongo', function () {
//     $config = $this->get('config')->mongodb;
//     $manager = new \MongoDB\Driver\Manager('mongodb://' . $config->host . ':' . $config->port);
//     return $manager;
// }, true);

//Collection manager
$di['collectionManager'] = function() {
  return new CollectionManager();
};
