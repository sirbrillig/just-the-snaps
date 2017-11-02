<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Factory function to create a default Asserter
 *
 * This creates an Asserter with a FileDriverDirectory for a specific directory
 * of snapshots. If you want to create an Asserter with a different driver,
 * it's necessary to build the Asserter manually.
 *
 * @param string $dirName The path to the snapshot directory
 * @return Asserter The Asserter
 */
function buildSnapshotAsserter(string $dirName) {
	$fileDriver = FileDriver::buildWithDirectory($dirName);
	return new Asserter($fileDriver);
}
