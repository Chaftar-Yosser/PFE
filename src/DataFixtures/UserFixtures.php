<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('yosser@yosser.fr');
        $user->setFirstname('yosser');
        $user->setLastname('yosser');
        $user->setFullname('yosseryosser');

        $hashedPassword = $this->hasher->hashPassword($user, 789);
        $user->setRole(["ROLE_ADMIN"]);
        $user->setFullname("yosser2");
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $manager->flush();
    }
}
