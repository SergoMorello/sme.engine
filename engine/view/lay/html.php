<!DOCTYPE HTML>
<html lang='ru'>
    <head>
        <title>@yield('title')</title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta name='description' content=''>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
		<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<style>@yield('style')</style>
    </head>
    <body>
		@yield('content')
	</body>
</html>