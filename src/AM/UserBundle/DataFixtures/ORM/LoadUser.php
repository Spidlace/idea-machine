<?php
// src/AM/UserBundle/DataFixtures/ORM/LoadUser.php

namespace AM\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AM\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Les noms d'utilisateurs à créer
        $listNames = array('admin@idea-machine.dev', 'user@idea-machine.dev', 'user1@idea-machine.dev');

        foreach ($listNames as $name) {
            $user = new User;

            $user->setUsername($name);
            $user->setPassword($name);

            $user->setSalt('');
            
            if($name == 'admin@idea-machine.dev'){
                $user->setRoles(array('ROLE_ADMIN'));
            } else {
                $user->setRoles(array('ROLE_USER'));
            }

            // On le persiste
            $manager->persist($user);
        }

        // On déclenche l'enregistrement
        $manager->flush();
    }
}