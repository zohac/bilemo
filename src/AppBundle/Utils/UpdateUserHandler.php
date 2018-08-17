<?php

namespace App\Utils;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateUserHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * Constructor.
     *
     * @param ObjectManager                $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(ObjectManager $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Undocumented function.
     *
     * @param User $userBeforeUpdate
     * @param User $user
     *
     * @return User
     */
    public function update(User $userUpdate, User $user): User
    {
        // 1) Update the user
        if ($userUpdate->getUsername()) {
            $user->setUsername($userUpdate->getUsername());
        }
        if ($userUpdate->getFirstname()) {
            $user->setFirstname($userUpdate->getFirstname());
        }
        if ($userUpdate->getLastname()) {
            $user->setLastname($userUpdate->getLastname());
        }
        if ($userUpdate->getEmail()) {
            $user->setEmail($userUpdate->getEmail());
        }
        if ($userUpdate->getPassword()) {
            $password = $this->passwordEncoder->encodePassword($user, $userUpdate->getPassword());
            $user->setPassword($password);
        }

        // 2) save the user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
