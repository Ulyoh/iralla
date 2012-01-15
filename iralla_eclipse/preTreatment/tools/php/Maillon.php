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
	
	public static function invert_between($first_maillon, $last_maillon) {
		if ($first_maillon != $last_maillon) {
			$next_first = $first_maillon->next;
			$next_last = $last_maillon->previous;
			$first_maillon->swap_with( $last_maillon );
			if ($next_first->next == $next_last) {
				$next_first->swap_with( $next_last );
				return;
			}
			Maillon::invert_between( $next_first, $next_last );
		}
	}
	
	public function remove() {
		if ($this->next != null)
			$this->next->previous = $this->previous;
		if ($this->previous != null)
			$this->previous->next = $this->next;
	}
	
	public function swap_with(Maillon $other, Maillon &$first_maillon = null) {
		//change with the next maillons:
		$buffer = $this->next;
		$this->next = $other->next;
		$other->next = $buffer;
		if (isset( $this->next ))
			$this->next->previous = ($this->next != null) ? $this : null;
		if (isset( $other->next ))
			$other->next->previous = ($other->next != null) ? $other : null;
		
		//change with the previous maillons
		$buffer = $this->previous;
		$this->previous = $other->previous;
		$other->previous = $buffer;
		if (isset( $this->previous ))
			$this->previous->next = ($this->previous != null) ? $this : null;
		if (isset( $other->previous ))
			$other->previous->next = ($other->previous != null) ? $other : null;
		
		if ($first_maillon != null) {
			if ($other->previous == null) {
				$first_maillon = $other;
			}
			if ($this->previous == null) {
				$first_maillon = $this;
			}
		}
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


