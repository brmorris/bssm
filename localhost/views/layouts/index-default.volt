<!DOCTYPE html>
<html>
	<head>
		<title>Simple Service Manager</title>
		<link href="//netdna.bootstrapcdn.com/bootswatch/2.3.2/flatly/bootstrap.min.css" rel="stylesheet">
		{{ stylesheet_link('css/style.css') }}
	</head>
	<body>

		{%- block content %}{% endblock -%}

		{{- assets.outputJs() -}}
	</body>
</html>
