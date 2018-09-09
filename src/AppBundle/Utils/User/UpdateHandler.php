<?php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Exception\FormValidationException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param ObjectManager         $entityManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        ObjectManager $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     *
     * @return User
     */
    public function handle(FormInterface $form, User $user): User
    {
        if ($form->isValid()) {
            // 1) Get the data from the form
            $userUpdate = $form->getData();

            // 2) Update the user
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

            // 5) save the user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        }

        // Check the contraint in the form
        throw new FormValidationException($form);
    }
}
