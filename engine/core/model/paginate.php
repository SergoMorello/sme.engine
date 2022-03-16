<?php
namespace SME\Core\Model;

use SME\Http\Request;
use SME\Support\View;

class Paginate {
	private $url, $view, $total, $count, $perPage, $currentPage, $totalPages;

	public function __construct($total, $perPage, $currentPage, $totalPages, $countPage) {
		$this->total = $total;
		$this->perPage = $perPage;
		$this->currentPage = $currentPage;
		$this->totalPages = $totalPages;
		$this->count = $countPage;
		$this->url = (!empty(Request::server('HTTPS')) ? 'https' : 'http').'://'.Request::server('HTTP_HOST').Request::server('PATH_INFO');
	}

	public function __init($view = null) {
		$this->view = $view;
		return $this;
	}

	public function __toString() {
		$system = true;
		$view = 'paginator';
		if (is_string($this->view)) {
			$system = false;
			$view = $this->view;
		}
		try {
			return (string)View::make($view, [
				'paginator' => $this
			], $system);
		} catch (\Throwable $e) {
			throw new \SME\Exceptions\Exception($e);
		}
	}

	public function lastPage() {
		return $this->totalPages;
	}

	public function count() {
		return $this->count;
	}

	public function total() {
		return $this->total;
	}

	public function perPage() {
		return $this->perPage;
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
		$page = $this->currentPage();
		$value = $page > 1 ? --$page : $page;
		return $this->requestPageVar($value);
	}

	public function nextPageUrl() {
		$page = $this->currentPage();
		$value = $page < $this->totalPages ? ++$page : $page;
		return $this->requestPageVar($value);
	}

	public function url($page) {
		return $this->requestPageVar($page);
	}
}