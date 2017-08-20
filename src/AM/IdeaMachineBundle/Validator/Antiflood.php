<?php
// src/AM/IdeaMachineBundle/Validator/Antiflood.php

namespace AM\IdeaMachineBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
	public $message = "Votre contenu fait moins de 3 caractères. Merci, d'en rajouter un peu.";
}