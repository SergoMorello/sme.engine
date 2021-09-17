<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <title>@yield('title')</title>
        <meta http-equiv='Content-Type' content='text/html charset=utf-8'>
        <meta name='description' content='SME - simple mvc engine'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
		<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
		<script src="{{asset('js/bootstrap.bundle.min.js')}}" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
		<style>@yield('style')</style>
    </head>
    <body>
		@yield('content')
	</body>
</html>