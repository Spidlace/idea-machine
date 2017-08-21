<?php

// src/AM/IdeaMachineBundle/Controller/IdeaController.php

namespace AM\IdeaMachineBundle\Controller;

use AM\IdeaMachineBundle\Entity\Idea;
use AM\IdeaMachineBundle\Entity\Image;
use AM\UserBundle\Entity\User;
use AM\IdeaMachineBundle\Form\IdeaType;
use AM\IdeaMachineBundle\Form\IdeaEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class IdeaController extends Controller
{
    public function indexAction()
    {   
        // On récupère le repositery
        $em = $this->getDoctrine()->getManager();
        $listIdeas = $em->getRepository('AMIdeaMachineBundle:Idea')->getIdeas();

        if(null === $listIdeas){
            throw new NotFoundHttpException("Aucunes idées éxistantes.");
        }

        $count = $em->getRepository('AMIdeaMachineBundle:Idea')->getCountAjax();

        return $this->render('AMIdeaMachineBundle:Idea:index.html.twig', 
            array('listIdeas' => $listIdeas, 'count' => $count)
        );
    }

    public function viewAction($slug)
    {
        // On récupère le repositery
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('AMIdeaMachineBundle:Idea');

        // Je récupère l'entité correspondante à $id
        $idea = $repository->findOneBy(array('slug' => $slug));

        if(null === $idea){
            throw new NotFoundHttpException("L'idée avec le slug : ".$slug." n'existe pas.");
        }

        // On récupère le nombre de vote de cette idée
        $nbrVotes = $em
        ->getRepository('AMIdeaMachineBundle:Vote')
        ->findBy(array('idea' => $idea));

        $nbrTotalVotes = 0;
        if(!empty($nbrVotes)){
            foreach ($nbrVotes as $vote){
                $choix = intval($vote->getChoix());
                $nbrTotalVotes = $nbrTotalVotes + $choix;
            }
        }

        return $this->render('AMIdeaMachineBundle:Idea:view.html.twig', 
            array(
                'idea'  => $idea,
                'nbrVotes'  => $nbrTotalVotes
            )
        );
    }

    public function addAction(Request $request)
    {

        // Si l'utilisateur n'a pas l'autorisation d'accéder à cette view
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
            return $this->redirectToRoute('am_idea_machine_home');
        }

        // On créé l'objet "Idea"
        $idea = new Idea();

        // On crée le Formbuilder grâce au service form factory
        $form = $this->get('form.factory')->create(IdeaType::class, $idea);

        // Création de l'entité Image
        // $image = new Image();
        // $image->setUrl('https://symfony.com/images/v5/opengraph/symfony_logo_vertical.png');
        // $image->setAlt('Logo de Symfony');

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){

            $user = $this->getUser();
            $idea->setUser($user);

            // On enregistre notre objet $idea dans la base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($idea);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Idée bien enregistrée.');

            // On redirige vers la page de visualisation de l'idée nouvellement créée
            return $this->redirectToRoute('am_platform_view', array('slug' => $idea->getSlug()));
        }

        return $this->render('AMIdeaMachineBundle:Idea:add.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    public function editAction($slug, Request $request)
    {   
        // Si l'utilisateur n'a pas l'autorisation d'accéder à cette view
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
            return $this->redirectToRoute('am_idea_machine_home');
        }

        // On récupère le repositery
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AMIdeaMachineBundle:Idea');

        // Je récupère l'entité correspondante à $id
        $idea = $repository->findOneBy(array('slug' => $slug));

        if($idea->getUser()->getID() != $this->getUser()->getId()){
            throw new AccessDeniedException("Vous n'avez pas l'autorisation d'éditer cette idée.");
            return $this->redirectToRoute('am_platform_view', array('slug' => $slug));
        } elseif(null === $idea){
            throw new NotFoundHttpException("L'idée avec le slug : ".$slug." n'existe pas.");
            return $this->redirectToRoute('am_idea_machine_home');
        }

        // On crée le Formbuilder grâce au service form factory
        $form = $this->get('form.factory')->create(IdeaEditType::class, $idea);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            // On enregistre notre objet $idea dans la base de données
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Idée bien modifiée.');

            // On redirige vers la page de visualisation de l'idée nouvellement créée
            return $this->redirectToRoute('am_platform_view', array('slug' => $idea->getSlug()));
        }

        return $this->render('AMIdeaMachineBundle:Idea:edit.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    public function deleteAction($slug, Request $request)
    {

        // Si l'utilisateur n'a pas l'autorisation d'accéder à cette view
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
            return $this->redirectToRoute('am_idea_machine_home');
        }

        // On récupère le repositery
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AMIdeaMachineBundle:Idea');

        // Je récupère l'entité correspondante à $id
        $idea = $repository->findOneBy(array('slug' => $slug));

        if($idea->getUser()->getID() != $this->getUser()->getId()){
            throw new AccessDeniedException("Vous n'avez pas l'autorisation de supprimer cette idée.");
            return $this->redirectToRoute('am_platform_view', array('slug' => $slug));
        } elseif(null === $idea){
            throw new NotFoundHttpException("L'idée avec le slug : ".$slug." n'existe pas.");
            return $this->redirectToRoute('am_platform_view', array($slug => $idea->getSlug()));
        }
        
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
          $em->remove($idea);
          $em->flush();

          $request->getSession()->getFlashBag()->add('info', "L'idée a bien été supprimée.");
            return $this->redirectToRoute('am_idea_machine_home');
        }

        // Idée supprimée
        return $this->render('AMIdeaMachineBundle:Idea:delete.html.twig', array(
            'idea' => $idea,
            'form'   => $form->createView(),
        ));
    }

    public function mineAction()
    {   

        // Si l'utilisateur n'a pas l'autorisation d'accéder à cette view
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
            return $this->redirectToRoute('am_idea_machine_home');
        }

        // On récupère le repositery
        $em = $this->getDoctrine()->getManager();
        $listIdeas = $em->getRepository('AMIdeaMachineBundle:Idea')->getIdeaUser($this->getUser()->getId());

        if(null === $listIdeas){
            throw new NotFoundHttpException("Aucunes idées éxistantes.");
        }

        $count = $em->getRepository('AMIdeaMachineBundle:Idea')->getCountAjax($this->getUser()->getId());

        return $this->render('AMIdeaMachineBundle:Idea:mine.html.twig', 
            array('listIdeas' => $listIdeas, 'count' => $count)
        );
    }

    public function getOtherIdeaAction(Request $request)
    {   

        if($request->isXmlHttpRequest())
        {
            $page = $request->request->get('page');
            $page_max = $request->request->get('page_max');
            $user_id = $request->request->get('user_id');
            if(empty($user_id)) $user_id = null;

            if(isset($page) && $page <= $page_max){
                $offset = $page*6;

                $em = $this->getDoctrine()->getManager();
                $listIdeas = $em->getRepository('AMIdeaMachineBundle:Idea')->getOtherIdea($offset, $user_id);

                return $this->render('AMIdeaMachineBundle:Idea:listIdeas-li.html.twig', 
                    array('listIdeas' => $listIdeas)
                );
            }
        }
        throw new NotFoundHttpException("Aucunes idées éxistantes.");
    }
}