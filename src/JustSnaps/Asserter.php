<?php

namespace JustSnaps;

class Asserter {
	private $driver;
	private $matcher;

	public function __construct( FileDriver $driver, Matcher $matcher ) {
		$this->driver = $driver;
		$this->matcher = $matcher;
	}

	public function assertMatchesSnapshot( string $testName, $actual ) {
		$expected = $this->driver->getSnapshotForTest( $testName );
		return $this->matcher->doesSnapshotMatch( $expected, $actual );
	}
}
