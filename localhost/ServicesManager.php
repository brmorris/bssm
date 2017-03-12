<?php

require_once __DIR__ . "/vendor/autoload.php";


class Services
{
  # Class to manage a number of "Services" that executening on this host.
  # Sevice definitions are loaded from a local JSON file.

  const JSON_FILE = './services.json';
  public $services = array();

  public function __construct()
    {
      try
      {
          $data = file_get_contents(self::JSON_FILE);
          $json = json_decode($data, true);
      } catch(Exception $e) {
          echo 'Error! Loading services json data from file: ', $e->getMessage();
      }

      foreach ($json as $service_id => $service_data) {
          $service_object = new Service();
          try
          {
            $service_object->name = $service_data['name'];
            $service_object->description = $service_data['description'];
            $service_object->category = $service_data['category'];
            $service_object->operations = $service_data['operations'];
            $this->services[] = $service_object;
          } catch(Exception $e) {
            echo 'Error! Invalid Service data: ', $e->getMessage();
          }
      }
    }

    public function getServiceByName ($name) {
      foreach ($this->services as $service) {
          if ( $service->name == $name ) {
              return $service;
          }
      }
      return;
    }

}

class Service
{
  # A class to manage a Single service.
  #
  # Services have a name, description, category and (optionally) a number
  # of "operations" that can be done on the service (eg: 'status'.
  # Each operation is an associative array with 'name and' 'command' fields.
  public $name;
  public $description;
  public $category;
  public $operations = array();

  public function getOperationByName($name) {
    foreach ($this->operations as $operation) {
        if ( $operation['name'] == $name ) {
            return $operation;
        }
    }
    return;
  }

  public function executeOperation($operation_name) {
    # Method to "execute" (or run) the operation in $operation_name.
    # returns an array containing the status, message, data
    $operation = $this->getOperationByName($operation_name);
    if ( $operation ) {
         $executor = new OperationExecutor();
         $executor->execute( $this, $operation );
         return $executor->results();
    } else {
         return array(404, "Operation not found: " . $operationname, "");
    }
  }

  public function as_json() {
    # returns a json structure of this Service
    return json_encode($this);
  }

  public function as_string() {
    # returns a string describing this Service
    return $this->name . " - ". $this->description . " (category: " . $this->category .")\n";
  }

}

class OperationExecutor
{
  #
  # A class to execute / run an Operation on a Service.
  #
  #
  #

  public $service;
  public $operation;
  public $exit_status;
  public $output;
  public $mongodbID; # the mongodb doc ID
  public $json_result = array(); # statuscode, message, data

  public function execute($service, $operation) {
     $this->service = $service;
     $this->operation = $operation;
     exec($operation['command'] . " 2>&1", $command_output, $this->exit_status);
     $this->output[] = join("\n", $command_output);
     $this->writeToMongoDB();
  }

  public function results (){
     $statuscode = 0;
     $message = "";
     switch ( $this->exit_status ) {
        case 0:
            $statuscode = 201;
            break;
        default:
            $statuscode = 404;
            $message = "Operation failed: check data field for details.";
     }
     $data = [ 'execution_id' => $this->mongodbID, 'output' => $this->output ];
     return array($statuscode, $message, $data);
  }

  private function writeToMongoDB() {
    # stores this operation execution to mongodb. There are a bunch of mongodb
    # wrappers available. To be clear, here is a link tot the docs for lib used
    # here: https://docs.mongodb.com/php-library/master/tutorial.
    try
    {
        $collection = (new MongoDB\Client)->services->logs;
    } catch(Exception $e) {
        echo 'Error! Could not connect to mongodb: ', $e->getMessage();
    }

    $insertOneResult  = $collection->insertOne([
        'service'     => $this->service->name,
        'category'    => $this->service->category,
        'operation'   => $this->operation['name'],
        'output'      => $this->output,
        'exit_status' => $this->exit_status
    ]);
    $this->mongodbID = (string)$insertOneResult->getInsertedId();
  }
}


class Executions
{
  #
  # A class to return "executions". Executions are just documents in mongodb
  # that show an "execution" of an operation on service (so it's like a log)
  #
  private $collection;

  public function __construct()
    {
      try
      {
          $this->collection = (new MongoDB\Client)->services->logs;
      } catch(Exception $e) {
          echo 'Error! Could not connect to mongodb: ', $e->getMessage();
      }
    }

  public function find() {
    try {
      // todo: expose find query options (limit, skip, sort, projection)
      // from https://docs.mongodb.com/php-library/master/reference/method/MongoDBCollection-find/#phpmethod.MongoDB\Collection::find
      $cursor = $this->collection->find([], ['limit' => 100, ]);
      $results = array();
      foreach ($cursor as $doc) {
         $results[] = $doc;
      };
      #echo "BRAD:";
      #var_dump($results);
      return $results;
    } catch(Exception $e) {
      return;
    }
    return $document;
  }

  public function findByID($id) {
    try {
      $id_obj = new MongoDB\BSON\ObjectID($id);
      $document = $this->collection->findOne(['_id' => $id_obj]);
    } catch(Exception $e) {
      return;
    }
    return $document;
  }

}

?>
