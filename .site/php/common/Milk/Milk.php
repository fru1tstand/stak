<?php
namespace common\Milk;
use common\Milk\inject\AbstractModule;
use common\Milk\inject\Injector;

/**
 * Here lies my poorly programmed implementation of Google's Java Guice.
 *
 * This package provides a light dependency injection framework that is based off Guice. It
 * promotes modularity within a project which drives the creation of better self-contained code.
 * It's also very useful for testing and code re-usability.
 * @package common\Milk
 */
class Milk {

	/**
	 * Creates an injector from
	 * @param AbstractModule ...$modules
	 * @return Injector
	 */
	public static function createInjector(AbstractModule... $modules) {
		$mapping = [];
		foreach ($modules as $module)
			$mapping = array_merge($mapping, $module->getMap());
		return new Injector($mapping);
	}

	// We're here to protect you from ever spilling milk by never letting you have any.
	private function __construct() {}
}