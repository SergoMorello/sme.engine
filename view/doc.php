@extends('lay.html')

@section('title','Документация | '.config()->APP_NAME)

@section('content')
	@include('inc.menu')
	<div class="container">
		<h1 class="fs-4">simple mvc engine</h1>
		<h2 class="fs-6 mb-5">Это нечто похожее на laravel но намного быстрее и проще</h2>
		<div class="accordion" id="accordionPanelsStayOpenExample">
			<div class="accordion-item">
				<h2 class="accordion-header" id="panelsStayOpen-heading1">
					<button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse1" aria-expanded="false" aria-controls="panelsStayOpen-collapse1">
					Route
					</button>
				</h2>
				<div id="panelsStayOpen-collapse1" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-heading1">
					<div class="accordion-body">
						<h4 class="fs-5 mb-4">В файле /route.php</h4>
						
						<div class="bg-light m-4">route::get('/','mainController@index')->name('home');</div>
						
						<h5 class="fs-6">Создаём маршрут по пути `/` и вызываем метод `index` в контроллере `mainController`, так же даём имя маршруту `home` в цепочке методов `->name()`</h5>
						
						<h5 class="fs-6">Так же можно создать маршрут с анонимной функцией</h5>
						
						<div class="bg-light m-4">
							<div>route::get('/',function() {</div>
							<div>	//Тут мы что то выполняем например можем показать какой то вид View('home')</div>
							<div>	return View('home');</div>
							<div>})->name('home')</div>
						</div>
						
						<h4 class="fs-5">Для отправки переменных в маршруте поместите их в фигурных скобках прямо в URL</h4>
						
						<div class="bg-light m-4">route::get('/catalog/item/{id}',functio...</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-item">
				<h2 class="accordion-header" id="panelsStayOpen-heading2">
					<button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse2" aria-expanded="false" aria-controls="panelsStayOpen-collapse2">
					Controller
					</button>
				</h2>
				<div id="panelsStayOpen-collapse2" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading2">
					<div class="accordion-body">
						<h4 class="fs-5">В папке /controller создаём файл контроллера, например `mainController.php` шаблон есть в файле `def.php`</h4>
						
						<h5 class="fs-6">Создаём нужный нам метод, например `index()`</h5>
						
						<div class="bg-light m-4">
							<div>public function index() {</div>
							<div>	//Тут мы что то выполняем например можем показать какой то вид View('home')</div>
							<div>	return View('home');</div>
							<div>}</div>
						</div>
						
						<h5 class="fs-6">Для получения данных от клиента используется глобальная функция `request()`</h5>
						
						<div class="fs-6 fw-bold text-muted">Получаем переменную из маршрута:</div>
						<div class="bg-light m-4">request()->route('id');</div>
						<div class="fs-6 fw-bold text-muted">Или</div>
						<div class="bg-light m-4">
							<div>route::get('/catalog/item/{id}',function($id) {</div>
							<div>//В переменной $id будет первая переменная из маршрута</div>
						</div>
						
						<div class="fs-6 fw-bold text-muted">Получаем переменную из формы:</div>
						<div class="bg-light m-4">request()->input('name');</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-item">
				<h2 class="accordion-header" id="panelsStayOpen-heading3">
					<button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse3" aria-expanded="false" aria-controls="panelsStayOpen-collapse3">
					View
					</button>
				</h2>
				<div id="panelsStayOpen-collapse3" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading3">
					<div class="accordion-body">
						<h4 class="fs-5">В папке /view создаём файл вида, например `home.php`</h4>
						
						<h5 class="fs-6">Передать переменную в вид из контроллера или анонимной функции маршрута</h5>
						
						<div class="bg-light m-4">View('home',['message'=>'hello','message2'=>'world']);</div>
						
						<h5 class="fs-6">В видах мы можем делать как и обычные вставки php `{{"<?php ?>"}}` так и компилируемые с помощью спецсимволов `@nc{{Переменная или функция}}@endnc` или `@функция`</h5>
						
						<div class="bg-light m-4">
							<div>{{"<h1>Hello World!!!</h1>"}}</div>
							<div>{{"<strong>"}}yes @nc{{$message}} {{$message2}}@endnc!{{"</strong>"}}</div>
							<div>Time: @nc{{date('H:i:s')}}@endnc</div>
						</div>
						
						<h5 class="fs-6">Наследование `lay` - папка, `html` - файл</h5>
						
						@nc
						<div class="bg-light m-4">@extends('lay.html')</div>
						@endnc
						
						<h5 class="fs-6">Обьявить секцию</h5>
						
						<div class="bg-light m-4">
							<div>@nc@section('content')@endnc</div>
							<div>{{"<div>Контент</div>"}}</div>
							<div>@nc@endsection@endnc</div>
						</div>
						
						<div class="fs-6 fw-bold text-muted">или</div>
						
						@nc
						<div class="bg-light m-4">@section('head','Текст')</div>
						
						<h5 class="fs-6">Получить секцию</h5>
						
						<div class="bg-light m-4">@yield('content')</div>
						
						<h5 class="fs-6">Обьявить переменную:</h5>
						
						<div class="bg-light m-4">
							<div>@php</div>
							<div>$var = 123;</div>
							<div>@endphp</div>
						</div>
						
						@endnc
						<h5 class="fs-6">Перебрать массив</h5>
						
						<div class="bg-light m-4">
							<div>{{"<ul>"}}</div>
							<div>@nc@foreach($items as $item)@endnc</div>
							<div>{{"<li>"}}@nc{{$item}}@endnc{{"</li>"}}</div>
							<div>@nc@endforeach@endnc</div>
							<div>{{"</ul>"}}</div>
						</div>
						
						<h5 class="fs-6">Добавление собственных функций в компилятор</h5>
						
						<div class="fs-6 fw-bold text-muted">В файле `appService.php` в методе `register` или же подключить свой класс через appService</div>
						
						<div class="fs-6 fw-bold text-muted">declareCompiller(`имя функции`,`анонимная функция(`агрументы переданные в функцию`,`последним всегда будет анонимная функция для добавления в конец буффера`)`)</div>
						@nc
						
						<div class="bg-light m-4">
							<div>self::declareCompiller('calc',function($arg1,$arg2,$appendFnc){</div>
							<div> $appendFnc('<?php echo '.($arg1+$arg2).'; ?>');</div>
							<div> return "<?php echo ".$arg1."; ?>";</div>
							<div>});</div>
						</div>
						
						@endnc
						<h5 class="fs-6">Готово, вызываем в виде</h5>
						
						<div class="bg-light m-4">@nc@calc(2,4)@endnc</div>
						
						<div class="fs-6 fw-bold text-muted">Результат, в том месте где была вызвана функция будет выведено `2`</div>
						
						<div class="fs-6 fw-bold text-muted">в самом низу страницы будет выведено `6`</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-item">
				<h2 class="accordion-header" id="panelsStayOpen-heading4">
					<button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse4" aria-expanded="false" aria-controls="panelsStayOpen-collapse4">
					Model
					</button>
				</h2>
				<div id="panelsStayOpen-collapse4" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading4">
					<div class="accordion-body">
						<h4 class="fs-5">В папке /model создаём файл модели, например `db.php` шаблон есть в файле `def.php`</h4>
						
						<h5 class="fs-6">По умолчанию имя таблицы должно быть таким же как и название класса модели, но можно переназначить с помощью свойства класса `$table`</h5>
						
						<div class="bg-light m-4">protected $table='other_table';</div>
						
						<h5 class="fs-6">Для работы с моделью нужно подключить её в контроллере</h5>
						
						<div class="bg-light m-4">
							<div>public function index() {</div>
							<div>$this->model("db");</div>
						</div>
						
						<h5 class="fs-6">Чтобы обратится к моделе используем свойство класса model->`имя модели`:</h5>
						
						<div class="bg-light m-4">$db = $this->model->db;</div>
						
						<h5 class="fs-6">Для работы с данными используется цепочка методов родительского класса:</h5>
						
						<div class="bg-light m-4">$db->select('name')->where('id',1)->first();</div>
						
						<h4 class="fs-5">Примеры:</h4>
						
						<h5 class="fs-6">Получаем массив:</h5>
						
						<div class="bg-light m-4">$db->select('name','test','status')->where('uid',1)->get();</div>
						
						<h5 class="fs-6">Создаём запись:</h5>
						
						<div class="bg-light m-4">
							<div>$db->name = 'name';</div>
							<div>$db->price = '123';</div>
							<div>$db->save();</div>
						</div>
						<h5 class="fs-6">Редактируем запись (метод `find()` используется для получения записи по `ID`):</h5>
						
						<div class="bg-light m-4">
							<div>$db->find(1);</div>
							<div>$db->name = 'name2';</div>
							<div>$db->price = '1234';</div>
							<div>$db->save();</div>
						</div>
						<h5 class="fs-6">Удаление записи:</h5>
						
						<div class="bg-light m-4">
							<div>$db->find(1);</div>
							<div>$db->delete();</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-item">
				<h2 class="accordion-header" id="panelsStayOpen-heading5">
					<button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse5" aria-expanded="false" aria-controls="panelsStayOpen-collapse5">
					Storage
					</button>
				</h2>
				<div id="panelsStayOpen-collapse5" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading5">
					<div class="accordion-body">
						<h4 class="fs-5">Класс для работы с хранилищем (в стадии разработки)</h4>
						
						<h5 class="fs-6">Сохранить файл на диск</h5>
						
						<div class="bg-light m-4">storage::disk('local')->put('file.txt','какие то данные');</div>
						
						<h5 class="fs-6">Получить файл с диска</h5>
						
						<div class="bg-light m-4">storage::disk('local')->get('file.txt');</div>
						
						<h5 class="fs-6">Удалить файл</h5>
						
						<div class="bg-light m-4">storage::disk('local')->delete('file.txt');</div>
						
						<h5 class="fs-6">Проверить существует ли файл</h5>
						
						<div class="bg-light m-4">storage::disk('local')->exists('file.txt');</div>
						
						<h5 class="fs-6">Так же можно сохранять файлы сразу из при их получении из формы</h5>
						
						<div class="bg-light m-4">
							<div>...</div>
							<div>{{'<input type="file" name="file" />'}}</div>
							<div>{{'<input type="text" name="fileName" value="default"/>'}}</div>
							<div>...</div>
						</div>
						
						<div class="bg-light m-4">request()->file('file')->storeAs('',request()->input('fileName').'.jpg');</div>
					</div>
				</div>
			</div>
			
			<div class="accordion-item">
				<h2 class="accordion-header" id="panelsStayOpen-heading6">
					<button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse6" aria-expanded="false" aria-controls="panelsStayOpen-collapse6">
					Cache
					</button>
				</h2>
				<div id="panelsStayOpen-collapse6" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading6">
					<div class="accordion-body">
						<h4 class="fs-5">В кэше можно хранить любые данные и файлы от одной секунды до бесконечности</h4>
						
						<h5 class="fs-6">Сохранить данные в кэше put(`ключ`,`данные`,`время хранения в секундах`)</h5>
						
						<div class="bg-light m-4">cache::put('message','Hello World!',60);</div>
						
						<h5 class="fs-6">Получить данные</h5>
						
						<div class="bg-light m-4">cache::get('message');</div>
						
						<div class="fs-6 fw-bold text-muted">или получить и сразу удалить</div>
						
						<div class="bg-light m-4">cache::pull('message');</div>
						
						<h5 class="fs-6">Удалить</h5>
						
						<div class="bg-light m-4">cache::forget('message');</div>
						
						<h5 class="fs-6">Проверить сущеутвование по ключу</h5>
						
						<div class="bg-light m-4">cache::has('message');</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
@endsection