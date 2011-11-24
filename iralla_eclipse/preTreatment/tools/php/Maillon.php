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
	
	public static function add_after($new_maillon, $cur_maillon) {
		$new_maillon->previous = $cur_maillon;
		$new_maillon->next = $cur_maillon->next;
		if ($new_maillon->next != null) {
			$new_maillon->next->previous = $new_maillon;
		}
		$cur_maillon->next = $new_maillon;
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
		$this->next->previous = ($this->next != null) ? $this : null;
		$other->next->previous = ($other->next != null) ? $other : null;
		
		//change with the previous maillons
		$buffer = $this->previous;
		$this->previous = $other->previous;
		$other->previous = $buffer;
		$this->previous->next = ($this->previous != null) ? $this : null;
		$other->previous->next = ($other->previous != null) ? $other : null;
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


