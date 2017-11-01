<?php
declare(strict_types=1);

namespace JustSnaps;

if (class_exists('\PHPUnit_Framework_IncompleteTestError')) {
	class CreatedSnapshotException extends \PHPUnit_Framework_IncompleteTestError {
	}
}
else if (class_exists('\PHPUnit\Framework\IncompleteTestError')) {
	class CreatedSnapshotException extends \PHPUnit\Framework\IncompleteTestError {
	}
} else {
	class CreatedSnapshotException extends \Exception {
	}
}
