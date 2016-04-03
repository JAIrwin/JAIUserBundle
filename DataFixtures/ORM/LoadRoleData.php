<?php
// src/AppBundle/DataFixtures/ORM/LoadRoleData.php

namespace JAI\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JAI\UserBundle\Entity\Role;

class LoadRoleData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $roleUser = new Role();
        $roleUser->setRole('ROLE_USER');

        $roleAdmin = new Role();
        $roleAdmin->setRole('ROLE_ADMIN');


        $manager->persist($roleUser);
        $manager->persist($roleAdmin);

        $manager->flush();
    }
}
