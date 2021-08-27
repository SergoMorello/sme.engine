#	SME

##	simple mvc engine

###	Это нечто похожее на laravel но намного быстрее и проще

###	Route:

####	В файле /route.php

`route::get('/','mainController@index')->name('home')`;

#####	Создаём маршрут по пути `/` и вызываем метод `index` в контроллере `mainController`, так же даём имя маршруту `home` в цепочке методов `->name()`

#####	Так же можно создать маршрут с анонимной функцией

`route::get('/',function() {`  
`	//Тут мы что то выполняем например можем показать какой то вид View('home')`  
`	return View('home');`  
`})->name('home')`

#### Для отправки переменных в маршруте поместите их в фигурных скобках прямо в URL

`route::get('/catalog/item/{id}',functio...`

###	Controller:

####	В папке /c создаём файл контроллера, например `mainController.php` шаблон есть в файле `def.php`

#####	Создаём нужный нам метод, например `index()`

`public function index() {`  
`	//Тут мы что то выполняем например можем показать какой то вид View('home')`  
`	return View('home');`  
`}`


##### Для получения данных от клиента используется глобальная функция `request()`

######	Получаем переменную из маршрута:
`request()->route('id');`
######	Или
`route::get('/catalog/item/{id}',function($id) {`  
`//В переменной $id будет первая переменная из маршрута`

######	Получаем переменную из формы:
`request()->input('name');`  

###	View:

####	В папке /v создаём файл вида, например `home.php`

#####	Передать переменную в вид из контроллера или анонимной функции маршрута

`View('home',['message'=>'hello','message2'=>'world']);`  

#####	В видах мы можем делать как и обычные вставки php `<?php ?>` так и компилируемые с помощью спецсимволов `{{Переменная или функция}}` или `@функция`

`<h1>Hello World!!!</h1>`  
`<strong>yes {{$message}} {{$message2}}!</strong>`  
`Time: {{date('H:i:s')}}`

#####	Наследование `lay` - папка, `html` - файл

`@extends('lay.html')`

#####	Обьявить секцию

`@section('content')`  
`<div>Контент</div>`  
`@endsection`  

######	или

`@section('head','Текст')`

#####	Получить секцию

`@yield('content')`


#####	Обьявить переменную:

`@php`  
`$var = 123;`  
`@endphp`  

#####	Перебрать массив

`<ul>`  
`@foreach($items as $item)`  
`<li>{{$item}}</li>`  
`@endforeach`  
`</ul>`

#####	Добавление собственных функций в компилятор

###### 	В файле `appService.php` в методе `register` или же подключить свой класс через appService

######	declareCompiller(`имя функции`,`анонимная функция(`агрументы переданные в функцию`,`последним всегда будет анонимная функция для добавления в конец буффера`)`)

`self::declareCompiller('calc',function($arg1,$arg2,$appendFnc){`  
` $appendFnc('<?php echo '.($arg1+$arg2).'; ?>');`  
` return "<?php echo ".$arg1."; ?>";`  
`});`  

#####	Готово, вызываем в виде

`@calc(2,4)`  

######	Результат, в том месте где была вызвана функция будет выведено `2`

######	в самом низу страницы будет выведено `6`

###	Model:

####	В папке /m создаём файл модели, например `db.php` шаблон есть в файле `def.php`

#####	По умолчанию имя таблицы должно быть таким же как и название класса модели, но можно переназначить с помощью свойства класса `$table`

`protected $table='other_table';`

#####	Для работы с моделью нужно подключить её в контроллере

`public function index() {`  
`$this->model("db");`

#####	Чтобы обратится к моделе используем свойство класса model->`имя модели`:

`$db = $this->model->db;`

#####	Для работы с данными используется цепочка методов родительского класса:

`$db->select('name')->where('id',1)->first();`

#### Примеры:

#####	Получаем массив:

`$db->select('name','test','status')->where('uid',1)->get();`

#####	Создаём запись:

`$db->name = 'name';`  
`$db->price = '123';`  
`$db->save();`

#####	Редактируем запись (метод `find()` используется для получения записи по `ID`):

`$db->find(1);`  
`$db->name = 'name2';`  
`$db->price = '1234';`  
`$db->save();`

#####	Удаление записи:

`$db->find(1);`  
`$db->delete();`