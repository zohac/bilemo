<?php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Exception\FormValidationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserPasswordHandlerService
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
     * @param TokenStorageInterface        $tokenStorage
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
     *
     * @return User
     */
    public function handle(FormInterface $form, User $user): User
    {
        if ($form->isValid()) {
            // 1) Get the data
            $data = $form->getData();

            // 2) Encode the password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

            // 3) save the user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        }

        // Check the contraint in the form
        throw new FormValidationException($form);
    }
}
