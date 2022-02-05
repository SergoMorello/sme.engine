<?php

class cookie {
	public function delete($data) {
		return setcookie($data, NULL);
	}
}