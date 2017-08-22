<?php

// src/AM/IdeaMachineBundle/Controller/IdeaController.php

namespace AM\IdeaMachineBundle\Controller;

use AM\IdeaMachineBundle\Entity\Idea;
use AM\IdeaMachineBundle\Entity\Image;
use AM\IdeaMachineBundle\Entity\Vote;
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

        // On récupère l'user
        $user_id = $this->getUser();

        $arrayIdeas = array();
        foreach ($listIdeas as $key => $idea){
            $arrayIdeas[$key]['slug'] = $idea->getSlug();
            $arrayIdeas[$key]['image'] = $idea->getImage();
            $arrayIdeas[$key]['id'] = $idea->getId();
            $arrayIdeas[$key]['title'] = $idea->getTitle();
            $arrayIdeas[$key]['content'] = $idea->getContent();
            $arrayIdeas[$key]['user'] = $idea->getUser();
    
            // On importe dans l'array le total des votes
            $countTotalVote = $em->getRepository('AMIdeaMachineBundle:Vote')->getCountVoteIdea($idea->getId());
            $arrayIdeas[$key]['votes'] = $countTotalVote;
    
            // On détecte sur l'utilisateur à déjà voté et on l'insère dans l'array
            if(!empty($user_id)) $alreadyVote = $em->getRepository('AMIdeaMachineBundle:Vote')->isUserAlreadyVote($this->getUser()->getId(), $idea->getId());
            else $alreadyVote = 1;
            $arrayIdeas[$key]['alreadyVote'] = $alreadyVote;

        }

        if(null === $listIdeas){
            throw new NotFoundHttpException("Aucunes idées éxistantes.");
        }

        $count = $em->getRepository('AMIdeaMachineBundle:Idea')->getCountAjax();

        return $this->render('AMIdeaMachineBundle:Idea:index.html.twig', 
            array('listIdeas' => $arrayIdeas, 'count' => $count)
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

        $user_id = $this->getUser();
        if(!empty($user_id)){
            $alreadyVote = $em->getRepository('AMIdeaMachineBundle:Vote')->isUserAlreadyVote($this->getUser()->getId(), $idea->getID());
        } else {
            $alreadyVote = 1;
        }

        return $this->render('AMIdeaMachineBundle:Idea:view.html.twig', 
            array(
                'idea'  => $idea,
                'nbrVotes'  => $nbrTotalVotes,
                'alreadyVote' => $alreadyVote
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
            $em = $this->getDoctrine()->getManager();
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

        $arrayIdeas = array();
        foreach ($listIdeas as $key => $idea){
            $arrayIdeas[$key]['slug'] = $idea->getSlug();
            $arrayIdeas[$key]['image'] = $idea->getImage();
            $arrayIdeas[$key]['id'] = $idea->getId();
            $arrayIdeas[$key]['title'] = $idea->getTitle();
            $arrayIdeas[$key]['content'] = $idea->getContent();
            $arrayIdeas[$key]['user'] = $idea->getUser();
    
            // On importe dans l'array le total des votes
            $countTotalVote = $em->getRepository('AMIdeaMachineBundle:Vote')->getCountVoteIdea($idea->getId());
            $arrayIdeas[$key]['votes'] = $countTotalVote;
    
            // On détecte sur l'utilisateur à déjà voté et on l'insère dans l'array
            $alreadyVote = $em->getRepository('AMIdeaMachineBundle:Vote')->isUserAlreadyVote($this->getUser()->getId(), $idea->getId());
            $arrayIdeas[$key]['alreadyVote'] = $alreadyVote;

        }

        if(null === $listIdeas){
            throw new NotFoundHttpException("Aucunes idées éxistantes.");
        }

        $count = $em->getRepository('AMIdeaMachineBundle:Idea')->getCountAjax($this->getUser()->getId());

        return $this->render('AMIdeaMachineBundle:Idea:mine.html.twig', 
            array('listIdeas' => $arrayIdeas, 'count' => $count)
        );
    }

    public function getOtherIdeaAction(Request $request)
    {   
        // Si la requète provient de la requète AJAX
        if($request->isXmlHttpRequest())
        {
            $page = $request->request->get('page');
            $page_max = $request->request->get('page_max');
            $user_id = $request->request->get('user_id');
            if(empty($user_id)) $user_id = null;

            if(isset($page) && $page <= $page_max){
                $offset = $page*6;

                // On récupère l'user
                $user_o = $this->getUser();

                $em = $this->getDoctrine()->getManager();
                $listIdeas = $em->getRepository('AMIdeaMachineBundle:Idea')->getOtherIdea($offset, $user_id);

                $arrayIdeas = array();
                foreach ($listIdeas as $key => $idea){
                    $arrayIdeas[$key]['slug'] = $idea->getSlug();
                    $arrayIdeas[$key]['image'] = $idea->getImage();
                    $arrayIdeas[$key]['id'] = $idea->getId();
                    $arrayIdeas[$key]['title'] = $idea->getTitle();
                    $arrayIdeas[$key]['content'] = $idea->getContent();
                    $arrayIdeas[$key]['user'] = $idea->getUser();
            
                    // On importe dans l'array le total des votes
                    $countTotalVote = $em->getRepository('AMIdeaMachineBundle:Vote')->getCountVoteIdea($idea->getId());
                    $arrayIdeas[$key]['votes'] = $countTotalVote;
            
                    // On détecte sur l'utilisateur à déjà voté et on l'insère dans l'array
                    if(!empty($user_o)) $alreadyVote = $em->getRepository('AMIdeaMachineBundle:Vote')->isUserAlreadyVote($user_o->getId(), $idea->getId());
                    else $alreadyVote = 1;
                    $arrayIdeas[$key]['alreadyVote'] = $alreadyVote;
                }

                return $this->render('AMIdeaMachineBundle:Idea:listIdeas-li.html.twig', 
                    array('listIdeas' => $arrayIdeas)
                );
            }
        }
        throw new NotFoundHttpException("Aucunes idées éxistantes.");
    }

    public function addVoteIdeaAction(Request $request)
    {   
        // Si la requète provient de la requète AJAX
        if($request->isXmlHttpRequest())
        {
            $vote_user = $request->request->get('vote');
            $item_id = $request->request->get('item_id');

            if((isset($vote_user) && ($vote_user == 1 || $vote_user == -1)) && (isset($item_id) && !empty($item_id))){

                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AMIdeaMachineBundle:Idea');
                $idea = $repository->findOneBy(array('id' => $item_id));

                // Création de l'entité
                $vote = new Vote();
                $vote->setIdea($idea);
                $vote->setChoix($vote_user);
                $vote->setUser($this->getUser());

                // On récupère l'EntityManager
                $em->persist($vote);
                $em->flush();

                $countTotalVote = $em->getRepository('AMIdeaMachineBundle:Vote')->getCountVoteIdea($idea->getId());

                return $this->render('AMIdeaMachineBundle:Vote:index.html.twig',
                    array('count_total' => $countTotalVote)
                );
            }
        }
        throw new NotFoundHttpException("Aucun vote d'indiqué.");
    }
}