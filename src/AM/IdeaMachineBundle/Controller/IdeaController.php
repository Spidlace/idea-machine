<?php

// src/AM/IdeaMachineBundle/Controller/IdeaController.php

namespace AM\IdeaMachineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IdeaController extends Controller
{
    public function indexAction()
    {
        $url = $this->generateUrl(
            'am_platform_view',
            array('id' => 5)
        );
        return new Response("L'URL de l'annonce d'id 5 est : ".$url);
    }

    public function viewAction($id)
    {
        return new Response("Affichage de l'annonce d'id : ".$id);
    }
}