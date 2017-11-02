# JustSnaps

A snapshot testing library for PHP

This library intends to be:

- Very low developer friction.
- Test runner agnostic.
- Lightweight.

**Still under development! API may change!**

## Installation

Coming soon...

## Generic Usage

This is how to use JustSnaps without any particular test runner.

For this example, assume we have a function called `getData()` which returns an array, and one called `assertTrue()` which throws an exception if its expression is not `true`.

```php
$actual = getData();
$asserter = \JustSnaps\buildSnapshotAsserter('./tests/__snapshots__');
assertTrue($asserter->forTest('testThatFooIsBar')->assertMatchesSnapshot($actual));
```

The first time this code is run, a `CreatedSnapshotException` will be thrown and the data being tested (`$actual`) will be serialized to the file `tests/__snapshots__/testThatFooIsBar.snap`.

The second time (and all subsequent times) this code is run, it will compare the data being tested with the contents of the snapshot file and return true or false depending on if it is the same.

This will protect against any regressions in `getData()`.

If the results of `getData()` change _intentionally_, then the test can be "reset" by simply deleting the snapshot file. The next time the test is run, it will re-create it as above.

## Serializers

JustSnaps will run `json_encode()` on any data before writing it to the snapshot file. If your data needs some manipulation before being written, you can create custom serializers.

Each serializer consists of two objects:

1. A class implementing `SerializerTester` which has one function: `shouldSerialize(mixed $data): bool`. This function must return true if the serializer should modify the data.
2. A class implementing `SerializerPrinter` which has one function: `serializeData(mixed $data): mixed`. This function can manipulate the data and then must return the new data which will be written to the snapshot file. Note that the data returned from `serializeData()` will still be passed through `json_encode()` prior to writing.

Here's an example of using a custom serializer to hide sensitive information.

```php
$actual = [ 'foo' => 'bar', 'secret' => 'thisisasecretpassword' ];
$printer = new class implements SerializerPrinter {
	public function serializeData($outputData) {
		$outputData['secret'] = 'xxx';
		return $outputData;
	}
};
$tester = new class implements SerializerTester {
	public function shouldSerialize($outputData): bool {
		return is_array($outputData) && isset($outputData['secret']);
	}
};
$serializer = new Serializer($tester, $printer);
$asserter = \JustSnaps\buildSnapshotAsserter('./tests/__snapshots__');
$asserter->addSerializer($serializer);
assertTrue($asserter->forTest('testThatFooIsBar')->assertMatchesSnapshot($actual));
```
