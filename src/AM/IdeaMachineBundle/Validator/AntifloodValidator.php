<?php
// src/AM/IdeaMachineBundle/Validator/AntifloodValidator.php

namespace AM\IdeaMachineBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		// Pour l'instant, on considère comme flood tout message de moins de 3 caractères
		if (strlen($value) < 3) {
			$this->context
			->buildViolation($constraint->message)
  			->setParameters(array('%string%' => $value))
			->addViolation($constraint->message);
		}
	}
}