<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $user = new User();
         $user->setFirstName('Ivona')
             ->setLastName('Iv')
             ->setEmail('iv@test.com')
             ->setPassword('$2a$12$jPtllGar.imGh23Afr0.iO5z1kw0SRevArBNCwNTu6X4ygQ4SOfDS')
             ->setPhoneNumber('12553')
             ->setPicture('me-6307c8f6b10ff.jpg')
             ->setIsVerified(true);

        ProductFactory::new()->createMany(30);

         $manager->persist($user);
         $manager->flush();
    }
}
