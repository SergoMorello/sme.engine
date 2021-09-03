@extends('lay.html')

@section('title','Error '.$code)

@section('content')
	<div class="container">
		<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading">{{$code}}</h4>
			<p>Error</p>
			<p class="mb-0">{{$message}}</p>
			@if(isset($sourceLines) && count($sourceLines))
				<hr>
				<p class="mb-0">
					@foreach($sourceLines as $key=>$line)
						<div {!!((($key+1)==$errorLine) ? 'class="alert-warning"' : NULL)!!}><b>#</b>{{$key+1}} {{$line}}</div>
					@endforeach
				</p>
			@endif
		</div>
	</div>
@endsection