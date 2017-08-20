<?php
// src/AM/UserBundle/Controller/UserController.php

namespace AM\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController
{
   	public function LoginBisAction()
	{
		$csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

		return $this->container->get('templating')->renderResponse('FOSUserBundle:Security:login_content.html.twig', array(
			'last_username' => null,
			'error'         => null,
			'csrf_token'    => $csrfToken
		));
	}
}