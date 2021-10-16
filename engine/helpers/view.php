<?php

function View($page,$data=array(),$code=200) {
	return view::show($page, $data, $code);
}