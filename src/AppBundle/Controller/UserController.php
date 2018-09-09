<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Swagger\Annotations as SWG;
use AppBundle\Form\User\CreateType;
use AppBundle\Form\User\UpdateType;
use AppBundle\Utils\User\CreateHandler;
use AppBundle\Utils\User\UpdateHandler;
use AppBundle\Utils\User\PasswordHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use AppBundle\Form\User\UpdatePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     *
     * @SWG\Get(
     *     description="Get the list of users.",
     *     tags = {"User"},
     *     @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: JWT Token not found / Expired JWT Token / Invalid JWT Token",
     *     ),
     *     @SWG\Response(
     *          response=405,
     *          description="Method Not Allowed"
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
     */
    public function listAction(ObjectManager $entityManager, UserInterface $user = null)
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
     *
     * @SWG\Get(
     *     description="Get one user.",
     *     tags = {"User"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=User::class),
     *          description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: JWT Token not found / Expired JWT Token / Invalid JWT Token",
     *     ),
     *     @SWG\Response(
     *          response=405,
     *          description="Method Not Allowed"
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          required= true,
     *          in="path",
     *          type="integer",
     *          description="The user unique identifier.",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
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
     *
     * @Rest\View(StatusCode = 201)
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Post(
     *     description="Create one user.",
     *     tags = {"User"},
     *     @SWG\Response(
     *          response=201,
     *          description="Created"
     *     ),
     *      @SWG\Response(
     *         response="400",
     *         description="Invalid json message received",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: JWT Token not found / Expired JWT Token / Invalid JWT Token",
     *     ),
     *     @SWG\Response(
     *          response=405,
     *          description="Method Not Allowed"
     *     ),
     *     @SWG\Parameter(
     *          name="Body",
     *          required= true,
     *          in="body",
     *          type="string",
     *          description="All property user to add",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=User::class, groups={"user"})
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
     */
    public function createAction(Request $request, CreateHandler $handler)
    {
        // Get the data POST
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(CreateType::class);

        // Submit the form
        $form->submit($data);

        // Create the user
        return $handler->handle($form);
    }

    /**
     * Update one user.
     *
     * @Rest\Patch(
     *      path="/api/users/{id}",
     *      name="users_update",
     *      requirements = {"id"="\d+"}
     * )
     * @ParamConverter("user",  options={"mapping"={"id"="id"}})
     *
     * @Rest\View(StatusCode = 200)
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Patch(
     *     description="Update one user.",
     *     tags = {"User"},
     *     @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *     ),
     *      @SWG\Response(
     *         response="400",
     *         description="Invalid json message received",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: JWT Token not found / Expired JWT Token / Invalid JWT Token",
     *     ),
     *     @SWG\Response(
     *          response=405,
     *          description="Method Not Allowed"
     *     ),
     *     @SWG\Parameter(
     *          name="Body",
     *          required= true,
     *          in="body",
     *          type="string",
     *          description="All property user to add",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=User::class, groups={"user"})
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
     */
    public function updateAction(Request $request, UpdateHandler $handler, User $user)
    {
        // Get the data POST
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(UpdateType::class);

        // Submit the form
        $form->submit($data);

        // Update the user
        return $handler->handle($form, $user);
    }

    /**
     * Update a user's password.
     *
     * @Rest\Patch(
     *      path="/api/users/password",
     *      name="users_update_password",
     *      requirements = {"id"="\d+"}
     * )
     *
     * @Rest\View(StatusCode = 200)
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Patch(
     *     description="Update a user's password.",
     *     tags = {"User"},
     *     @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *     ),
     *      @SWG\Response(
     *         response="400",
     *         description="Invalid json message received",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: JWT Token not found / Expired JWT Token / Invalid JWT Token",
     *     ),
     *     @SWG\Response(
     *          response=405,
     *          description="Method Not Allowed"
     *     ),
     *     @SWG\Parameter(
     *          name="Body",
     *          required= true,
     *          in="body",
     *          type="string",
     *          description="All property user to add",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=User::class, groups={"user"})
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
     */
    public function updatePasswordAction(Request $request, PasswordHandler $handler, UserInterface $user)
    {
        // Get the data POST
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(UpdatePasswordType::class);

        // Submit the form
        $form->submit($data);

        // Update the user
        return $handler->handle($form, $user);
    }

    /**
     * @Rest\Delete(
     *      path="/api/users/{id}",
     *      name="users_delete",
     *      requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 204)
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Delete(
     *     description="Delete one user.",
     *     tags = {"User"},
     *     @SWG\Response(
     *          response=204,
     *          description="No Content"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: JWT Token not found / Expired JWT Token / Invalid JWT Token",
     *     ),
     *     @SWG\Response(
     *          response=405,
     *          description="Method Not Allowed"
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          required= true,
     *          in="path",
     *          type="integer",
     *          description="The user unique identifier.",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
     */
    public function deleteAction(User $user, ObjectManager $entityManager)
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return;
    }
}
