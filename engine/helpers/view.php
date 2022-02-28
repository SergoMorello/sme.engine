<?php

function View($page,$data=array(),$code=200) {
	return SME\Core\View\view::show($page, $data, $code);
}