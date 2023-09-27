<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
        parent::__construct($registry, User::class);
    }

    public function updatePassword(User $user, string $password)
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $this->_em->persist($user);
        $this->_em->flush();
    }

}