<?php

require_once('ServicesManager.php');

$services = new Services();
$executions = new Executions();

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

define ('APP_PATH', realpath('.'));
require APP_PATH . '/di.php';

require_once __DIR__ . "/vendor/autoload.php";

$app = new Micro();
$app->setDI($di); # $di is created in di.php

#
# Send a structured API response
#
function sendAPIResponse ($statuscode = 200, $message = "", $data = "") {
      $response = new Response();
      $response->setContentType('application/json', 'UTF-8');
      $response->setStatusCode($statuscode);
      $json = array('status' => $statuscode, 'message' => $message, 'data' => $data);
      $response->setContent(json_encode($json, JSON_PRETTY_PRINT));
      $response->send();
}

$app->get(
    "/api/services",
    function () use ($services, $app) {
          sendAPIResponse(200, "", $services);
    }
);

$app->get(
    "/api/service/{name}",
    function ($name) use ($services) {
        $service = $services->getServiceByName($name);
        if ( $service ){
           # todo, convert this to a ternary
           sendAPIResponse(200, "", $service);
        } else {
           sendAPIResponse(404, "Service not found: " . $name, "");
        }
    }
);

$app->get(
    "/api/service/{servicename}/operation/{operationname}",
    function ($servicename, $operationname ) use ($services) {
        $service = $services->getServiceByName($servicename);
        if ( $service ){
            $operation = $service->getOperationByName($operationname);
            if ( $operation ) {
                # todo, convert this to a ternary
                 sendAPIResponse(200, "", $operation);
            } else {
                 sendAPIResponse(404, "Operation not found: " . $operationname, "");

            }
        } else {
             sendAPIResponse(404, "Service not found: " . $name, "");
        }

    }
);

$app->post(
    "/api/service/{servicename}/operation/{operationname}",
    function ($servicename, $operationname ) use ($services) {
        $service = $services->getServiceByName($servicename);
        if ( $service ){
            $results = $service->executeOperation($operationname);
            # in php, it appears you have to unroll arrays returned
            # from functions (like an array deref)?
            sendAPIResponse($results[0], $results[1], $results[2]);
        } else {
            sendAPIResponse(404, "Service not found: " . $name, "");
        }
    }
);

#
# REST API calls for getting "Executions".
# See ServicesManager::Executions() for implementation details.
#
$app->get(
    "/api/execution/{id}",
    function ($id) use ($executions) {
        $document = $executions->findByID($id);
        if ( $document ){
           # todo, convert this to a ternary
           sendAPIResponse(200, "", $document);
        } else {
           sendAPIResponse(404, "Execution ID not found: " . $id, "");
        }
    }
);

$app->get(
    "/api/executions",
    function () use ($executions) {
        $data = $executions->find();
        if ( $data ){
           # todo, convert this to a ternary
           sendAPIResponse(200, "", $data);
        } else {
           sendAPIResponse(404, "Executions not found.");
        }
    }
);

#
# Utility urls
#
$app->get(
    "/ping",
    function () {
        echo "pong";
    }
);

$app->map('/', function() use ($app) {
  echo $app->view->render('dashboard', array());
});

# TODO
#  executions "service locking" in reddis

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'Not found.';
});

$app->handle();

