<?php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Exception\FormValidationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateHandler
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param ObjectManager                $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        ObjectManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     */
    public function handle(FormInterface $form): User
    {
        if ($form->isValid()) {
            $user = $form->getData();

            // Set the Customer
            $user->setCustomer($this->tokenStorage->getToken()->getUser()->getCustomer());
            // Encode the password
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            // Set Role
            $user->setRoles(['ROLE_USER']);

            // 5) save the user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        }

        // Check the contraint in the form
        throw new FormValidationException($form);
    }
}
