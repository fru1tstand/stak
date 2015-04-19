<?php
namespace common\Milk\inject;
use \Exception;
use \ReflectionClass;

/**
 * Where the magic happens. This is used to create instances of the requested abstract class or
 * interface.
 * @package common\Milk\inject
 */
class Injector {
	/* @var Binder[] */	private $mapping;
	/* @var object[] */	private $singletonInstances;


	/**
	 * Creates an injector from the given mappings
	 * @param Binder[] $mapping
	 * @throws Exception
	 */
	public function __construct(array $mapping) {
		foreach ($mapping as $map)
			if (!($map instanceof Binder))
				throw new Exception("Mapping must be an array of binders");

		$this->mapping = array();
		$this->singletonInstances = array();
		foreach ($mapping as $map) {
			$this->mapping[$map->getAbstract()] = $map;
			$refClass = new ReflectionClass($map->getAbstract());
			while ($refClass = $refClass->getParentClass()) {
				$this->mapping[$refClass->getName()] = $map;
			}
			if ($map->isSingleton())
				$this->singletonInstances[$map->getImplementation()] = null;
		}
	}

	/**
	 * Where the magic happens
	 * @param $name
	 * @return mixed
	 * @throws Exception Thrown when one or more Milk errors occur
	 */
	public function getInstance($name) {
		if (!class_exists($name) && !interface_exists($name))
			throw new Exception("'$name' doesn't exist.");
		if (!isset($this->mapping[$name]))
			throw new Exception("$name is unknown to me");

		$map = $this->mapping[$name];
		if ($map->isSingleton() && !is_null($this->singletonInstances[$map->getImplementation()]))
			return $this->mapping[$map->getImplementation()];

		$reflectionClass = new ReflectionClass($this->mapping[$name]->getImplementation());

		$resultObject = null;
		if (is_null($reflectionClass->getConstructor())
				|| count($reflectionClass->getConstructor()->getParameters()) == 0)
			$resultObject = $reflectionClass->newInstance();

		// TODO: Finish injector
		// Didn't find a class
		if (is_null($resultObject))
			throw new Exception("Finish the job please");

		if ($this->mapping[$name]->isSingleton())
			$this->singletonInstances[$name] = $resultObject;

		return $resultObject;
	}
}