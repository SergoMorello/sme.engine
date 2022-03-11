@if($paginator->lastPage() > 1)
	<nav>
		<ul class="pagination">
			@if($paginator->currentPage() <= 1)
				<li class="page-item disabled">
					<span class="page-link">Previous</span>
				</li>
			@else
			<li class="page-item">
				<a href="{{$paginator->previousPageUrl()}}" class="page-link">Previous</a>
			</li>
			@endif
			@for($i = 1; $i <= $paginator->lastPage(); $i++)
				@if($paginator->currentPage() == $i)
				<li class="page-item active" aria-current="page"><span class="page-link">{{$i}}</span></li>
				@else
				<li class="page-item" aria-current="page">
					<a href="{{$paginator->url($i)}}" class="page-link" href="#">{{$i}}</a>
				</li>
				@endif
			@endfor
			@if($paginator->currentPage() == $paginator->lastPage())
				<li class="page-item disabled">
					<span class="page-link" href="#">Next</span>
				</li>
			@else
				<li class="page-item">
					<a href="{{$paginator->nextPageUrl()}}" class="page-link" href="#">Next</a>
				</li>
			@endif
		</ul>
	</nav>
@endif