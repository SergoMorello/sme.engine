@extends('lay.html')

@section('content')
<div>123</div>
@if($errors->count())
@foreach($errors as $error)
<div>{{$error}}</div>
@endforeach
@endif


<form method="post" action="{{route('submit')}}" enctype="multipart/form-data">
<div><input type="text" name="tt" value="{{old('tt', 'test')}}" autocomplete="off"></div>
<div><input type="file" name="file[]" multiple></div>
<div><input type="checkbox" name="checkbox" {{old('checkbox') ? 'checked' : null}}></div>
<input type="submit" value="ok">
</form>
@endsection