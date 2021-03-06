<?php

namespace mageekguy\atoum\phpunit\constraints;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\phpunit\constraint,
	mageekguy\atoum\tools\variable\analyzer,
	mageekguy\atoum\asserter\exception
;

class greaterThan extends constraint
{
	private $expected;
	private $analyzer;

	public function __construct($expected, $description = null, analyzer $analyzer = null)
	{
		$this->expected = $expected;
		$this->description = $description;
		$this->analyzer = $analyzer ?: new analyzer();
	}

	protected function matches($actual)
	{
		if (is_float($actual))
		{
			$asserter = new asserters\phpFloat();
		}
		else
		{
			$asserter = new asserters\integer();
		}


		$asserter->setWith($actual)->isGreaterThan($this->expected);
	}
}
