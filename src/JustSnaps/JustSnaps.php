<?php
declare(strict_types=1);

namespace JustSnaps;

function buildWithDirectory(string $dirName) {
	$fileDriver = FileDriver::buildWithDirectory($dirName);
	return new Asserter($fileDriver, new Matcher());
}
