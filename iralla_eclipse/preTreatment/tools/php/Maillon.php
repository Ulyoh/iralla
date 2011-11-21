<?php
class Maillon {
	public $previous;
	public $next;
	
	public static function add_before($new_maillon, $cur_maillon) {
		$new_maillon->next = $cur_maillon;
		$new_maillon->previous = $cur_maillon->previous;
		if ($new_maillon->previous != null) {
			$new_maillon->previous->next = $new_maillon;
		}
		$cur_maillon->previous = $new_maillon;
	}
	
	public function remove() {
		$this->next->previous = $this->previous;
		$this->previous->next = $this->next;
	}
	
	public function swap_with(Maillon $other) {
		//change with the next maillons:
		$buffer = $this->next;
		$this->next = $other->next;
		$other->next = $buffer;
		if ($this->next != null)
			$this->next->previous = $this;
		if ($other->next != null)
			$other->next->previous = $other;
		
		//change with the previous maillons
		$buffer = $this->previous;
		$this->previous = $other->previous;
		$other->previous = $buffer;
		if ($this->previous != null)
			$this->previous->next = $this;
		if ($other->previous != null)
			$other->previous->next = $other;
	}
	
	public function __construct($previous = null, $next = null) {
		$this->previous = $previous;
		$this->next = $next;
		if ($previous != null) {
			$previous->next = $this;
		}
		if ($next != null) {
			$next->previous = $this;
		}
	}
}


