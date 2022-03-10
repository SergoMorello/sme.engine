<?php
namespace SME\Core\Model;

use SME\Http\Request;

class Paginate {
	private $url, $total, $count, $perPage, $currentPage, $totalPages;

	public function __construct($total, $perPage, $currentPage, $totalPages) {
		$this->total = $total;
		$this->perPage = $perPage;
		$this->currentPage = $currentPage;
		$this->totalPages = $totalPages;
		$this->url = (!empty(Request::server('HTTPS')) ? 'https' : 'http').'://'.Request::server('HTTP_HOST').Request::server('PATH_INFO');
	}

	public function __init($view = null, $count = 0) {
		$this->count = $count;
		return $this;
	}

	public function count() {
		return $this->count;
	}

	public function total() {
		return $this->total;
	}

	public function perPage() {
		return $this->perPage = $perPage;
	}

	public function currentPage() {
		return $this->currentPage;
	}

	private function requestPageVar($value = 1) {
		parse_str(Request::server('QUERY_STRING'), $return);
		$return['page'] = $value;
		return $this->url.'?'.http_build_query($return);
	}

	public function previousPageUrl() {
		$value = $this->currentPage > 1 ? --$this->currentPage : $this->currentPage;
		return $this->requestPageVar($value);
	}

	public function nextPageUrl() {
		$value = $this->currentPage < $this->totalPages ? ++$this->currentPage : $this->currentPage;
		return $this->requestPageVar($value);
	}
}