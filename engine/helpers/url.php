<?php

function url() {
	return new class{
		public function current() {
			return \SME\Http\Request::server('REQUEST_URI');
		}
	};
}