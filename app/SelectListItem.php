<?php

namespace App;

class SelectListItem {
	public $value;
	public $text;
	public $selected;

	public function __construct($name, $id, $selectedId) {
		$this->value = $id;

		$this->text = $name;

		$this->selected = ($id == $selectedId);
	}
}
