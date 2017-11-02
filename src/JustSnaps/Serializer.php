<?php
declare(strict_types=1);

namespace JustSnaps;

class Serializer {
	private $tester;
	private $printer;

	public function __construct(SerializerTester $tester, SerializerPrinter $printer) {
		$this->tester = $tester;
		$this->printer = $printer;
	}

	public function shouldSerialize($data): bool {
		return $this->tester->shouldSerialize($data);
	}

	public function serializeData($data) {
		return $this->printer->serializeData($data);
	}

	public function apply($data) {
		if ($this->shouldSerialize($data)) {
			return $this->serializeData($data);
		}
		return $data;
	}

	public static function applySerializers(array $serializers, $data) {
		return array_reduce($serializers, function ($serializedData, $serializer) {
			return $serializer->apply($serializedData);
		}, $data);
	}
}
