<?php

namespace controllers;


class Controller {

	public $data;

	/** @var \lib\Bootstrap */
	public $bootstrap;

	public function __construct($bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	public function setData($key, $value) {
		$this->data[$key] = $value;
	}

	public function getData($key, $default = false) {
		$return = $default;
		if (isset($this->data[$key])) {
			$return = $this->data[$key];
		}
		return $return;
	}

	public function render($view, $ext='.php') {

		include('../views/' . $view . $ext);

	}

} 