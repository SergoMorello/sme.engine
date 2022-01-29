@extends('lay.html')

@section('title',config('app.appName'))

@section('content')
	@include('inc.menu')
	<div class="container">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">{{config('app.appName')}}</h5>
				<h6 class="card-subtitle mb-2 text-muted">simple mvc engine</h6>
				<p class="card-text">{{$info}}</p>
				<p class="card-text">{{$text}}</p>
				@foreach($links as $el)
					<a href="{{$el['link']}}" target="_blank" class="card-link">{{$el['name']}}</a>
				@endforeach
			</div>
		</div>
	</div>
@endsection