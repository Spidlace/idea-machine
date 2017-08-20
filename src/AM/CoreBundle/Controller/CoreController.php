<?php

// src/AM/CoreBundle/Controller/CoreController.php

namespace AM\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function menuFooterAction()
    {
        $menuFooter = array(
            array('title' => 'Lorem'),
            array('title' => 'Ipsum'),
            array('title' => 'Copyright')
        );

        return $this->render('AMCoreBundle:Core:menuFooter.html.twig', array(
            'menuFooter' => $menuFooter
        ));
    }
}