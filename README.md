daff-php
========

This contains the haxe-generated php code for daff.

Tests
=========

Tests require the use of PHPUnit which can be installed via composer. To install PHPUnit to run the tests use the following command.

	composer update --dev

Tests are located in the ./test directory, to run all tests simply use the following command

	vendor/phpunit/phpunit/phpunit --bootstrap test/bootstrap.php test

Similarly you can run a single test using the following command. (eg: running the `SomeFileTest.php` test case)

	vendor/phpunit/phpunit/phpunit --bootstrap test/bootstrap.php test/SomeFileTest
