<?php

// src/AM/IdeaMachineBundle/Form/IdeaEditType.php

namespace AM\IdeaMachineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class IdeaEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->get('image')->setRequired(false);
    }

    public function getParent()
    {
        return IdeaType::class;
    }


}
