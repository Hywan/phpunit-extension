<?php

namespace mageekguy\atoum\phpunit\constraints;

use PHPUnit;
use
	mageekguy\atoum\asserter\exception,
	mageekguy\atoum\asserters,
	mageekguy\atoum\phpunit\constraint,
	mageekguy\atoum\tools\variable\analyzer;

class contains extends constraint
{
	private $analyzer;
	private $expected;
	private $ignoreCase;
	private $checkForObjectIdentity;
	private $checkForNonObjectIdentity;

	public function __construct($expected, $description = null, $ignoreCase = null, $checkForObjectIdentity = null, $checkForNonObjectIdentity = null, analyzer $analyzer = null)
	{
		$this->analyzer = $analyzer ?: new analyzer();
		$this->expected = $expected;
		$this->description = $description;
		$this->ignoreCase = (bool) $ignoreCase;
		$this->checkForObjectIdentity = $checkForObjectIdentity === null ? true : (bool) $checkForObjectIdentity;
		$this->checkForNonObjectIdentity = (bool) $checkForNonObjectIdentity;
	}

	protected function matches($actual)
	{
		$expected = $this->expected;

		switch (true)
		{
			case $this->analyzer->isArray($actual):
				$asserter = new asserters\phpArray(null, $this->analyzer);
				$asserter->setWith($actual);
				break;

			case $actual instanceof \iterator:
				$asserter = new asserters\iterator(null, $this->analyzer);
				$asserter = $asserter->setWith($actual)->toArray;
				break;

			case $this->analyzer->isString($actual):
				$asserter = new asserters\phpString(null, $this->analyzer);

				if ($this->ignoreCase)
				{
					$actual = strtolower($actual);
					$expected = strtolower($expected);
				}

				$asserter->setWith($actual);
				break;

			default:
				throw new PHPUnit\Framework\Exception('Actual value of ' . __CLASS__ . ' must be an array, a string or a traversable object');
		}

		try
		{
			if ($this->analyzer->isObject($expected))
			{
				if ($this->checkForObjectIdentity)
				{
					$asserter->strictlyContains($expected);
				}
				else
				{
					$asserter->contains($expected);
				}
			}
			else
			{
				if ($this->checkForNonObjectIdentity)
				{
					$asserter->strictlyContains($expected);
				}
				else
				{
					$asserter->contains($expected);
				}
			}
		}
		catch (exception $exception)
		{
			throw new exception($asserter, $this->analyzer->getTypeOf($actual) . ' does not contain ' . $this->analyzer->getTypeOf($expected));
		}
	}
}
