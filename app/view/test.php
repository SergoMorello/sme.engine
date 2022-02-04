@extends('lay.html')


@section('title', 'sdf')

@section('content')

@if(true)
@foreach(['123','321',456] as $numbers)
<div>{{$numbers}}</div>
@endforeach
@endif
@endsection