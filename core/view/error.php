@extends('lay.html')

@section('title','Error '.$code)

@section('content')
	<h1>Error:</h1>
	<h2>{{$code}}</h2>
	<h3>{{$message}}</h3>
@endsection