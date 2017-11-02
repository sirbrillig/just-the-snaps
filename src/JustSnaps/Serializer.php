<?php
declare(strict_types=1);

namespace JustSnaps;

/**
 * Tool to serialize data before being written to a snapshot
 *
 * Each Serializer requires two functions, wrapped in instances of
 * SerializerTester and SerializerPrinter. When applying this Serializer to
 * data, the data will be first passed to the SerializerTester's
 * shouldSerialize method. If that method returns true, the SerializerPrinter's
 * serializeData method will be called on the data before returning it.
 */
class Serializer {
	private $tester;
	private $printer;

	public function __construct(SerializerTester $tester, SerializerPrinter $printer) {
		$this->tester = $tester;
		$this->printer = $printer;
	}

	/**
	 * Apply this Serializer to data
	 *
	 * When calling this method, the data will be first passed to the
	 * SerializerTester's shouldSerialize method. If that method returns true,
	 * the SerializerPrinter's serializeData method will be called on the data
	 * before returning it.
	 *
	 * @param mixed $data The data being serialized
	 * @return mixed The serialized data, possibly unchanged
	 */
	public function apply($data) {
		if ($this->shouldSerialize($data)) {
			return $this->serializeData($data);
		}
		return $data;
	}

	private function shouldSerialize($data): bool {
		return $this->tester->shouldSerialize($data);
	}

	private function serializeData($data) {
		return $this->printer->serializeData($data);
	}

	/**
	 * Apply multiple serializers to some data
	 *
	 * @param array $serializers An array of Serializers to apply
	 * @param mixed $data The data being serialized
	 * @return mixed The serialized data, possibly unchanged
	 */
	public static function applySerializers(array $serializers, $data) {
		return array_reduce($serializers, function ($serializedData, $serializer) {
			return $serializer->apply($serializedData);
		}, $data);
	}
}
