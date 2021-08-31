@extends('lay.html')

@section('title',app()->config->APP_NAME)

@section('content')
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">{{app()->config->APP_NAME}}</a>
		</div>
	</nav>
	<div class="container">
		<div class="row">
			<div class="w-50 mx-auto">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">{{app()->config->APP_NAME}}</h5>
						<h6 class="card-subtitle mb-2 text-muted">simple mvc engine</h6>
						<p class="card-text">{{$info}}</p>
						<a href="https://github.com/SergoMorello/sme.engine" class="card-link">Github</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection