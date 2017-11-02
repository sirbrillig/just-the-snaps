<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Factory to create intances of FileDriverProvider
 */
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

	public static function addSerializer(Serializer $serializer, FileDriverProvider $provider): FileDriverProvider {
		if ($provider instanceof FileDriverWithSerializer) {
			$provider->addSerializer($serializer);
			return $provider;
		}
		return new FileDriverWithSerializer($serializer, $provider);
	}
}
