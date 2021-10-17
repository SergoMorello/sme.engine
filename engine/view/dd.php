@extends('lay.html')

@section('title','debug')

@section('style')
	.block {
		color:#353535;
		padding: 10px;
		font-size:15px;
		border-bottom:1px dotted #212121;
	}
	.array, .object {
		padding: 2px 0 2px 10px;
		font-size: 14px;
	}
	.array:first-child, .object:first-child {
		padding: 0;
	}
	.array > div, .object > div {
		padding: 0 0 0 10px;
	}
	.type {
		color: #CCC;
		font-size: 14px;
		padding: 0 4px 0 0;
	}
	.key {
		font-weight: bold;
		color: #ffc107;
	}
	.numeric {
		color: #084298;
	}
	.string {
		color: #6c757d;
	}
	.string:before, .string:after {
		content: '"';
	}
@endsection

@section('content')
	<div class='block'>
		{!!$data!!}
	</div>
@endsection