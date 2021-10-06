<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm mb-3 bg-body rounded">
	<div class="container-fluid">
		<a class="navbar-brand" href="{{route('home')}}">
			<img src="{{asset('ico/32x32.png')}}" alt="" width="32" height="32" class="d-inline-block align-text-top">
			{{config()->APP_NAME}}
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link {{route()->getName()=='doc' ? 'active' : null}}" aria-current="page" href="{{route('doc')}}">Документация</a>
				</li>
			</ul>
		</div>
	</div>
</nav>