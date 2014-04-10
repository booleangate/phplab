<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Score {
	private $user;
	private $value;
	private $ctime;

	public function __construct(User $user = null, $value = null, $ctime = null) {
		$this->setUser($user);
		$this->setValue($value);
		$this->setCtime($ctime);
	}

	public function setUser($user) {
		$this->user = $user;
		return $this;
	}

	public function getUser() {
		return $this->user;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	public function setCtime($ctime) {
		$this->ctime = $ctime;
		return $this;
	}

	public function getCtime() {
		return $this->ctime;
	}

	public function __toString() {
		return __CLASS__ . " [user={$this->user}, value={$this->value}, ctime=" . date("Y/m/d H:i:s", $this->ctime) . "]";
	}
}
