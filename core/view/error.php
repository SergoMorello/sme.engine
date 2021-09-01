@extends('lay.html')

@section('title','Error '.$code)

@section('content')
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">{{app()->config->APP_NAME}}</a>
		</div>
	</nav>
	<div class="container">
		<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading">{{$code}}</h4>
			<p>Error</p>
			<hr>
			<p class="mb-0">{{$message}}</p>
		</div>
	</div>
@endsection