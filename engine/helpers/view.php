<?php

function View($page, $data = [], $code = 200) {
	return \SME\Core\View\View::make($page, $data, $code);
}