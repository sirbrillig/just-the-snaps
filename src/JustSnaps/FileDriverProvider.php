<?php
declare(strict_types=1);

namespace JustSnaps;

interface FileDriverProvider {
	public function getSnapshotForTest(string $testName);
}
