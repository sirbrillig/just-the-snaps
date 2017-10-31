<?php

namespace JustSnaps;

class FileDriver {
	private $cachedData;

	public function __construct( $data = null ) {
		$this->cachedData = $data ?? [];
	}

	public static function buildWithData( $data ) {
		return new FileDriver( $data );
	}

	public function getSnapshotForTest( $testName ) {
		return $this->cachedData[ $testName ] ?? null;
	}
}
