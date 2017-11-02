<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Interface for a Serializer test function
 *
 * Intended to be paired with a SerializerPrinter in an instance of Serializer.
 *
 * If the shouldSerialize method returns true, the associated SerializerPrinter
 * will be applied to the data.
 */
interface SerializerTester {
	public function shouldSerialize($data): bool;
}
