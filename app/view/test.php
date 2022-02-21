@extends('lay.html')

@section('content')
<div>123</div>
@if($errors->count())
@foreach($errors as $error)
<div>{{$error}}</div>
@endforeach
@endif
<form method="post" action="{{route('submit')}}">
<div><input type="text" name="tt" autocomplete="off"></div>
<input type="submit" value="ok">
</form>
@endsection