<?php

namespace JustSnaps;

class Matcher {
	public function doesSnapshotMatch($original, $actual) {
		return ( $original === json_encode($actual) );
	}
}
