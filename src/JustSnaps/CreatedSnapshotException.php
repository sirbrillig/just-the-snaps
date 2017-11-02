<?php
declare(strict_types=1);

namespace JustSnaps;

if (class_exists('\PHPUnit_Framework_IncompleteTestError')) {
	class_alias(\PHPUnit_Framework_IncompleteTestError::class, 'JustSnaps\IncompleteTestError');
} elseif (class_exists('\PHPUnit\Framework\IncompleteTestError')) {
	class_alias(\PHPUnit\Framework\IncompleteTestError::class, 'JustSnaps\IncompleteTestError');
} else {
	class_alias(\Exception::class, 'JustSnaps\IncompleteTestError');
}

class CreatedSnapshotException extends IncompleteTestError {
}
