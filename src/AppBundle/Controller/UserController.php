<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends FOSRestController
{
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
}
