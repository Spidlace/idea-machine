<?php

namespace AM\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AMUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}