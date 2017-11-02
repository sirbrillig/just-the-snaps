<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Interface for snapshot drivers
 *
 * Each driver must be able to perform CRUD operations on its snapshots.
 */
interface FileDriverProvider {
	public function getSnapshotForTest(string $testName): string;
	public function doesSnapshotExistForTest(string $testName): bool;
	public function createSnapshotForTest(string $testName, $actual): void;
	public function removeSnapshotForTest(string $testName): void;
}
