<?php
declare(strict_types=1);

namespace JustSnaps;

function buildSnapshotAsserter(string $dirName) {
	$fileDriver = FileDriver::buildWithDirectory($dirName);
	return new Asserter($fileDriver);
}
