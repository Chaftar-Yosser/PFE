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
        $user->setEmail('test@test.test');
        $user->setFirstname('test');
        $user->setLastname('1');

        $hashedPassword = $this->hasher->hashPassword($user, 123);
        $user->setRole(["ROLE_ADMIN"]);
        $user->setFullname("test1");
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $manager->flush();
    }
}
