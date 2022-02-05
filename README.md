<h1>
  <img src="https://sme.inmsk.net/ico/32x32.png">
  <span>SME</span>
</h1>

##	simple mvc engine

###	Это нечто похожее на laravel но намного быстрее и проще

[Первый запуск](#first)  

[Route](#route)  
[Controller](#controller)  
[View](#view)  
[Compressor](#compressor)  
[Model](#model)  
[Storage](#storage)  
[Cache](#cache)  
[Http client](#http_client)  
[Exceptions](#exceptions)  
[Log](#log)  
[Console](#console)  

###

<h3 id="first">Первый запуск:</h3>

#### В директории с проектом просто выполнить команду

```php
php console serve
```

#### Готово!  Теперь можно открыть браузер по адресу `http://127.0.0.1:8000`
#### у вас должна открыться стандартная страница движка.

#### Всё ваше приложение находится в папке `app`
#### Маршруты в файле `routes/web.php`

<h3 id="route">Route:</h3>

####	В файле /route/web.php

```php 
route::get('/','mainController@index')->name('home');
```

#####	Создаём маршрут по пути `/` и вызываем метод `index` в контроллере `mainController`, так же даём имя маршруту `home` в цепочке методов `->name()`

#####	Так же можно создать маршрут с замыканием
```php
route::get('/',function() {  
  //Тут мы что то выполняем например можем показать какой то вид View('home') 
  return View('home');
})->name('home');
```

#### Для отправки переменных в маршруте поместите их в фигурных скобках прямо в URL
```php
route::get('/catalog/item/{id}',functio...
```
<h3 id="controller">Controller:</h3>

####	В папке /controller создаём файл контроллера, например `mainController.php` шаблон есть в файле `def.php`

#####	Создаём нужный нам метод, например `index()`
```php
public function index() {
  //Тут мы что то выполняем например можем показать какой то вид View('home')
  return View('home');
}
```


##### Для получения данных от клиента используется класс `request` или хелпер`request()`

######	Получаем переменную из маршрута:
```php
request()->route('id');
```
######	Или
```php
route::get('/catalog/item/{id}',function($id) {
  //В переменной $id будет первая переменная из маршрута
```

######	Получаем переменную из формы:
```php
request()->input('name');
```
<h3 id="view">View:</h3>

####	В папке /app/view создаём файл вида, например `home.php`

#####	Передать переменную в вид из контроллера или замыкания маршрута

```php
View('home',['message'=>'hello','message2'=>'world']);
```

#####	В видах мы можем делать как и обычные вставки php `<?php ?>` так и компилируемые с помощью спецсимволов `{{Переменная или функция}}` или `@функция`

```blade
<h1>Hello World!!!</h1>
<strong>yes {{$message}} {{$message2}}!</strong>
Time: {{date('H:i:s')}}
```
#####	Наследование `lay` - папка, `html` - файл

```blade
@extends('lay.html')
```

#####	Обьявить секцию

```blade
@section('content')  
<div>Контент</div> 
@endsection
```

######	или

```blade
@section('head','Текст')
```

#####	Получить секцию

```blade
@yield('content')
```


#####	Обьявить переменную:

```blade
@php  
$var = 123; 
@endphp
```

#####	Перебрать массив

```blade
<ul>
@foreach($items as $item)
<li>{{$item}}</li>
@endforeach
</ul>
```

#####	Добавление собственных функций в компилятор

###### 	В файле `appService.php` в методе `register` или же подключить свой класс через appService

######	compiler::declare(`имя функции`,`анонимная функция(`агрументы переданные в функцию`,`последним всегда будет анонимная функция для добавления в конец буффера`)`)

```php
compiler::declare('plus',function($arg1,$arg2,$appendFnc){ 
  $appendFnc('<?php echo '.$arg1.'; ?>');
  return "<?php echo ".($arg1+$arg2)."; ?>";
});
```

#####	Готово, вызываем в виде

```blade
@plus(2,4)
```

######	Результат, в том месте где была вызвана функция будет выведено `6`

######	в самом низу страницы будет выведено `2`

<h3 id="compressor">Compressor:</h3>

### Compressor - инструмент для обьединения подключаемых css или js файлов в один

#### Просто передаём компрессору наши файлы в виде массива и указываем какой файл должен получиться

##### Пример с css

```blade
<link href="{{compressor(['css/style.css', 'css/style2.css'], 'styles.css')}}" rel="stylesheet">
```
#### В результате получаем всего один файл что положительно сказывается на скорости загрузки страницы

##### Если содержимое файлов отлично от `text/javascript` или `text/css` можем указать своё в 3 агрументе функции


<h3 id="model">Model:</h3>

####	В папке /app/model создаём файл модели, например `db.php` шаблон есть в файле `def.php`

#####	По умолчанию имя таблицы должно быть таким же как и название класса модели, но можно переназначить с помощью свойства класса `$table`

```php
protected $table='other_table';
```

#####	Для работы с моделью нужно подключить её в контроллере

```php
public function index() {
$this->model("db");
...
```
##### Или если нужно использовать в замыкании маршрута

```php
controller::model("db");
```

#####	Чтобы обратится к модели используем тот же метод только без аргументов: model()->`имя модели`:

```php
$db = $this->model()->db;
```

##### Так же можно обратится к модели сразу после подключения

```php
controller::model("db")->find(1)->delete()
```

#####	Для работы с данными используется цепочка методов родительского класса:

```php
$db->select('name')->where('id',1)->first();
```

#### Примеры:

#####	Получаем массив:

```php
$db->select('name','test','status')->where('uid',1)->get();
```

#####	Создаём запись:

```php
$db->name = 'name';
$db->price = '123';
$db->save();
```

#####	Редактируем запись (метод `find()` используется для получения записи по `ID`):

```php
$db->find(1);
$db->name = 'name2';
$db->price = '1234';
$db->save();
```

#####	Удаление записи:

```php
$db->find(1);
$db->delete();
```


<h3 id="storage">Storage:</h3>

#### Класс для работы с хранилищем (в стадии разработки)

##### Сохранить файл на диск

```php
storage::disk('local')->put('file.txt','какие то данные');
```

##### Получить файл с диска

```php
storage::disk('local')->get('file.txt');
```

##### Удалить файл

```php
storage::disk('local')->delete('file.txt');
```

##### Проверить существует ли файл

```php
storage::disk('local')->exists('file.txt');
```

##### Так же можно сохранять файлы сразу из при их получении из формы

```php
...
<input type="file" name="file" />
<input type="text" name="fileName" value="default"/>
...
```
```php
request()->file('file')->storeAs('',request()->input('fileName').'.jpg');
```

<h3 id="cache">Cache:</h3>

#### В кэше можно хранить любые данные и файлы от одной секунды до бесконечности

##### Сохранить данные в кэше put(`ключ`,`данные`,`время хранения в секундах`)

```php
cache::put('message','Hello World!',60);
```

##### Получить данные

```php
cache::get('message');
```

###### или получить и сразу удалить

```php
cache::pull('message');
```

##### Удалить

```php
cache::forget('message');
```

##### Проверить сущеутвование по ключу

```php
cache::has('message');
```

<h3 id="http_client">Http client:</h3>

#### Простой GET запрос
```php
$response = http::get('http://url');
```
##### В ответ получаем обьект:

```php
$response->body(); //Тело ответа
$response->json(); //Если запрашивали json можно сразу преобразовать в массив
$response->header('имя заголовка'); //Получить заголовок из ответа
$response->headers(); //Получить все заголовки в виде массива
$response->ok(); //Если всё хорошо то true
$response->successful(); //Если код от 200 до 299
$response->failed(); //Если код от 400 до 499
$response->clientError(); //Если код 400
$response->serverError(); //Если код 500
```

#### POST запрос с параметрами
```php
http::post('http://url',['name'=>'value']);
```

##### По умолчанию запрос выполняется в виде json обьекта с типом `application/json`
##### Если нужно выполнить обычный `application/x-www-form-urlencoded` то добавьте метод asForm перед выполнением запроса
```php
http::asForm()->post('http://url',['name'=>'value']);
```
##### Если нужен `multipart/form-data`
```php
http::asMultipart()->post('http://url',['name'=>'value']);
```

#### Basic авторизация
```php
http::withBasicAuth('user', 'password')->get('http://url');
```
##### или Digest авторизация
```php
http::withDigestAuth('user', 'password')->get('http://url');
```
#####  если Realm статичен, можем указать
```php
http::withDigestAuth('user', 'password')->withRealm('realm')->get('http://url');
```

#### Таймаут в секундах
```php
http::timeout('20')->get('http://url');
```

#### Вызвать исключение в случае ошибки
```php
http::post('http://url',['name'=>'value'])->throw();
```
##### Если нужно обработать ошибку, можно использовать замыкание
```php
http::post('http://url',['name'=>'value'])->throw(function($response, $error){
  die("ой, что-то пошло не так");
});
```


<h3 id="exceptions">Exceptions:</h3>

#### Обьявить исключение можно в appService.php
```php
exceptions::declare('имя_исключения',function($data=""){
  return response('что то сломалось '.$data);
});
```
#### Вызываем исключение
```php
exceptions::throw('имя_исключения', 'Обновите страницу');
```
##### Или через хелпер
```php
abort('имя_исключения');
```
##### Так же мы можем переназначать системные исключения
###### Например переназначим исключение валидации на вывод json
```php
exceptions::declare('validate',function($errors){
  return response()->json([
          'status'=>false,
          'errors'=>$errors
   ]);
});
```

<h3 id="log">Log:</h3>

#### Логирование

##### Включить логирование можно в файле конфигурации `.env`

```
...
#	Logs
LOG_ENABLED=true
...
```
###### По умолчанию лог сохраняется по пути `ROOT/storage/.log/`

##### Сохранить информацию в лог

```php
log::info('Какой то вывод');
```

##### Если нужно записать ошибку используется метод `error`

```php
log::error('Ошибка');
```

##### Лог так же выводится в консольных командах
###### Например если мы хотим вывести таймер или что то подобное можно использовать метод `thisLine` в этом случае информация не запишется в файл а будет перезаписываться на этой же строке в консоле

```php
log::thisLine(true)->info(date('s'));
```

<h3 id="console">Console:</h3>

#### Команды

##### Запустить Dev server
```
php console serve
```

##### Очистить кэш
```
php console cache:clear
```

#### Запуск приложения из консоли

##### Например напишем отображение времени в консоли

###### В файле `route/console.php` создаём маршрут с методом `console` с замыканием
```php
route::console('time',function(){
  while(true) { //Создаём вечный цикл
    log::thisLine(true)->info(date('H:i:s')); //Выводим текущее время и смещаем каретку в начало
    sleep(1); //Ставим задержку на выполнение в 1 секунду
  }
});
```
###### Запускаем в консоли
```
php console time
```

###### Передача аргументов из консоли
```php
route::console('hello:{arg1}',function($arg1){
  log::info('Hello '.$arg1);
  //или
  log::info('Hello '.request()->route('arg1'));
...
```
###### Выполняем
```
php console hello:world
```
