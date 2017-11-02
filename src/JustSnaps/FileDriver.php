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

	/**
	 * Add a serializer to a FileDriverProvider
	 *
	 * @param Serializer $serializer The Serializer to add
	 * @param FileDriverProvider $driver The driver to which to add the Serializer
	 * @return FileDriverProvider The modified FileDriverProvider
	 */
	public static function addSerializerToDriver(Serializer $serializer, FileDriverProvider $provider): FileDriverProvider {
		if ($provider instanceof FileDriverWithSerializer) {
			$provider->addSerializer($serializer);
			return $provider;
		}
		return new FileDriverWithSerializer($serializer, $provider);
	}
}
