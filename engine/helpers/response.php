<?php

function response($data=NULL,$code=200) {
	return is_null($data) ? new response($data,$code) : $data;
}