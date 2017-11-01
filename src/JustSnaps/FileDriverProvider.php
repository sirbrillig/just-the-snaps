<?php
declare(strict_types=1);

namespace JustSnaps;

interface FileDriverProvider {
	public function getSnapshotForTest(string $testName): string;
	public function doesSnapshotExistForTest(string $testName): bool;
	public function createSnapshotForTest(string $testName, $actual): void;
	public function removeSnapshotForTest(string $testName): void;
}
