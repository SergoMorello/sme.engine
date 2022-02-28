<?php

function View($page,$data=array(),$code=200) {
	return SME\Core\View\View::show($page, $data, $code);
}