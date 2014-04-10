<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class User {
	private $userId;

	public function __construct($id = null) {
		$this->setId($id);
	}

	public function setId($id) {
		$this->id = (int)$id;
		return $this;
	}

	public function getId() {
		return $this->id;
	}

	public function __toString() {
		return __CLASS__ . " [id={$this->id}]";
	}
}
