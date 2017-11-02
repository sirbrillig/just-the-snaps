<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Interface for a Serializer function
 *
 * Intended to be paired with a SerializerTester in an instance of Serializer.
 *
 * If the associated shouldSerialize method returns true the serializeData
 * method will be applied to the data.
 */
interface SerializerPrinter {
	public function serializeData($data);
}
