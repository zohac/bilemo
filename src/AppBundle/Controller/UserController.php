<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get(
     *      path="/api/users",
     *      name="users_list"
     * )
     *
     * @Rest\View(statusCode = 200)
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function list(ObjectManager $entityManager, UserInterface $user = null)
    {
        return $entityManager->getRepository(User::class)->findAllWhithAllEntities($user);
    }

    /**
     * @Rest\Get(
     *      path="/api/users/{id}",
     *      name="users_show",
     *      requirements = {"id"="\d+"}
     * )
     * @Entity("user", expr="repository.findOneWhithAllEntities(id)")
     *
     * @Rest\View(statusCode = 200)
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function detailAction(User $user)
    {
        return $user;
    }

    /**
     * @Rest\Post(
     *      path="/api/users",
     *      name="users_detail",
     * )
     * @ParamConverter(
     *      "user",
     *      converter="fos_rest.request_body",
     *      options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     *
     * @Rest\View(StatusCode = 201)
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function create(
        User $user,
        UserInterface $userOrigin = null,
        UserPasswordEncoderInterface $encoder,
        ObjectManager $entityManager,
        ConstraintViolationList $violations
    ) {
        // Check the contraint in user entity
        if (count($violations)) {
            throw new ResourceValidationException($violations);
        }

        // Set the Customer
        $user->setCustomer($userOrigin->getCustomer());
        // Encode the password
        $password = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        // Set Role
        $user->setRoles(['ROLE_USER']);

        // Save the new user
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
