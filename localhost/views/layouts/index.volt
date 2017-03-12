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
					{{ link_to('/', "Brad's Simple Service Manager", 'class': 'hero') }}</a></h1>
				</div>
			</div>
		</div>

		{%- block content %}{% endblock -%}

		{{- assets.outputJs() -}}
	</body>
</html>
