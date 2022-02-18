<?php

route::get("/","main@index")->name('home');

route::get("/doc","main@doc")->name('doc');