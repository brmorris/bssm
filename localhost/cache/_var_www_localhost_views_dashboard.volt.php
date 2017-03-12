<!DOCTYPE html>
	<head>
		<title>Brad's Simple Service Manager</title>
		<script src="https://code.jquery.com/jquery-3.1.1.js"   integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA="   crossorigin="anonymous"></script>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
		<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" />
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>
	</head>

	<body>
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container">
					<h1><a class="btn btn-navbar" data-target=".nav-collapse" data-toggle="collapse">
					<?= $this->tag->linkTo(['/', 'Brad\'s Simple Service Manager', 'class' => 'hero']) ?></a></h1>
				</div>
			</div>
		</div>

<?= $this->flash->output() ?>

<script>

      function viewExecution(id){
          // open a modal with the results of the execution
          $.get("/api/execution/"+id, function(data, status){
              $("#modal-show-operation-label").text("Executed " + data.data.operation + " on " + data.data.service );
              $("#modal-show-operation-id").text("ID: " + id);
              $("#modal-show-operation-category").text("Category: " + data.data.category);
              $("#modal-show-operation-output").text(data.message + "\n" + data.data.output);
              $("#modal-show-operation-status").text("Exit status: "+ data.data.exit_status + " HTTP status: " + data.status);
              $("#modal-show-operation").modal({ show: true});
          });
      }
      function runOperation(opLink) {
          var op = opLink.id.split("-");
          var url = "/api/service/"+op[0]+"/operation/"+op[1];
          $.post(url, {}, function(data, status){
              updateExecutions();
              viewExecution(data.data.execution_id);
          });
      };
      function updateExecutions() {
        // updates the Executions log data in the Logs tab
         $.get("/api/executions/", function(data, status){

              var gridData = [];
              for (var docIndex in data.data) {
                  var doc = {
                    "Service": data.data[docIndex].service,
                    "Operation": data.data[docIndex].operation,
                    "Category": data.data[docIndex].category,
                    "Exit Status": data.data[docIndex].exit_status,
                    "ID": data.data[docIndex]._id.$oid,
                  };
                  gridData.push(doc);
              }
              $("#jsGrid").jsGrid({
                    width: "100%",
                    height: "400px",
                    inserting: false,
                    editing: false,
                    sorting: true,
                    paging: true,
                    rowClick: function(item) {
                        viewExecution(item.item.ID);
                    },
                    data: gridData,
                    fields: [
                        { name: "Service", type: "text", width: 150, validate: "required" },
                        { name: "Operation", type: "text", width: 150, validate: "required" },
                        { name: "Category", type: "text", width: 150, validate: "required" },
                        { name: "Exit Status", type: "number", width: 50 },
                        { name: "ID", type: "textarea", autosearch: true, readOnly: true },
                    ],

                });
          });
      };

      $(document).ready(function(){

          $('#modal-show-operation').modal({ show: false});

          String.prototype.supplant = function (o) {
              // helper functoin to do string interpolation. Taken from SO
              return this.replace(/{([^{}]*)}/g,
                  function (a, b) {
                      var r = o[b];
                      return typeof r === 'string' || typeof r === 'number' ? r : a;
                  }
              );
          };

          // populate the services list
          $.get("/api/services", function(data, status){
              services = data.data.services;
              for (var service_id in services) {
                  service = services[service_id];
                  operation_links = "<ul>";
                  for (operation_id in service.operations){
                      opname = service.operations[operation_id].name;
                      operation_links += '<li><a href="#" onclick="runOperation(this)" id="{service}-{opname}">{opname}</a></li>'.supplant( {service: service.name, opname: opname});
                  }
                  operation_links += "</ul>";
                  operation_html = '<button data-toggle="collapse" data-target="#{service}">{service}</button> <p> \
                            {description} \
                            <div id="{service}" class="collapse"> \
                            {operation_links} \
                           </div>'.supplant({ description: service.description, service: service.name, operation_links: operation_links });

                  $(".services-list").append(operation_html);
              }
          });
          updateExecutions();
      });

</script>

<!-- Modal -->
<div id="modal-show-operation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modal-show-operation-label">Service operation log</h3>
    </div>
    <div class="modal-body">
        <pre id="modal-show-operation-status">0</pre>
        <hr>
        <pre id="modal-show-operation-output">One fine body…</pre>
    </div>
    <div class="modal-footer">
        <small id="modal-show-operation-category"></small>
        <small id="modal-show-operation-id"></small>
        <button type="button" class="btn btn-primary disabled">Rerun</button>
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
  </div>
</div>
	<div align="">

    <div class="container">
      <ul class="nav nav-pills">
          <li class="active">
            <a  href="#services" data-toggle="tab">Services</a>
          </li>
          <li><a href="#logs" data-toggle="tab">Logs</a>
          </li>
          <li><a href="#api" data-toggle="tab">API Docs</a>
          </li>
      </ul>
      <hr>

      <div class="tab-content clearfix">
            <div class="tab-pane active" id="services">
              <h3>Services</h3>
              Click on a service to run an operation:
              <p>
              <ul class="services-list">
              </ul>
            </div>
            <div class="tab-pane" id="logs">
              <h3>Operation Execution Logs</h3>
              <div id="jsGrid"></div>
            </div>
            <div class="tab-pane" id="api">

<h3>API Documentation</h3>

<h4>REST API Results format</h4>

The rest api at `/api` returns the following JSON format:

<pre>
{
    "status": 200,
    "message": "",
    "data": {
    }
}
</pre>

where <pre>message</pre> is optional and the format of the <pre>data</pre> field depends on the API call.

<h4>Services API</h4>

Get all services:

<pre>GET <a href="/api/services">/api/services</a></pre>

Get one service:

<pre>GET <a href="/api/service/ufw">/api/service/:servicename</a></pre>

<h4>Operations API</h4>

Get one operation:

<pre>GET <a href="/api/service/ufw/operation/status">/api/service/:servicename/operation/:operationname</a></pre>

Run an operation:

<pre>POST /api/service/:servicename/operation/:operationname</pre>

<h4>Executions API</h4>

Get last 100 exececutions:

<pre>GET <a href="/api/executions">/api/executions</a></pre>

Get one execution:

<pre>GET /api/execution/:id</pre>

            </div>
          </div>
      </div>
	</div>



<?= $this->assets->outputJs() ?></body>
</html>
