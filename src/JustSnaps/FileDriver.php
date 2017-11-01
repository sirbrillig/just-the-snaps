<?php
declare(strict_types=1);

namespace JustSnaps;

class FileDriver {
	public static function buildWithData(array $data): FileDriverProvider {
		return new FileDriverArray($data);
	}

	public static function buildWithDirectory(string $dirName): FileDriverProvider {
		return new FileDriverDirectory($dirName);
	}

	public static function makeReadOnly(FileDriverProvider $provider): FileDriverProvider {
		return new FileDriverReadOnlyWrapper($provider);
	}
}
