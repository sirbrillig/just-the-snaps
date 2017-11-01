<?php
declare(strict_types=1);

namespace JustSnaps;

interface SerializerTester {
	public function shouldSerialize($data): bool;
}
