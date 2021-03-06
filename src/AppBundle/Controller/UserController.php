<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Swagger\Annotations as SWG;
use AppBundle\Form\User\UserCreateType;
use AppBundle\Form\User\UserUpdateType;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\User\UserUpdatePasswordType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Service\User\UserCreateHandlerService;
use AppBundle\Service\User\UserUpdateHandlerService;
use AppBundle\Service\User\UserPasswordHandlerService;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends FOSRestController
{
    /**
     * Get the list of users.
     *
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
        $users = $entityManager->getRepository(User::class)->findAllWhithAllEntities($user);

        return $this->view($users, Response::HTTP_OK);
    }

    /**
     * Get one user.
     *
     * @Rest\Get(
     *      path="/api/users/{id}",
     *      name="users_show",
     *      requirements = {"id"="\d+"}
     * )
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
    public function detailAction(ObjectManager $entityManager, UserInterface $user = null, int $id)
    {
        $user = $entityManager->getRepository(User::class)->findOneWhithAllEntities($user, $id);

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * Create one user.
     *
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
    public function createAction(Request $request, UserCreateHandlerService $userCreateHandler)
    {
        // Get the data POST
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(UserCreateType::class);

        // Submit the form
        $form->submit($data);

        // Create the user
        return $this->view($userCreateHandler->handle($form), Response::HTTP_CREATED);
    }

    /**
     * Update one user.
     *
     * @Rest\Patch(
     *      path="/api/users",
     *      name="users_update"
     * )
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
    public function updateAction(Request $request, UserUpdateHandlerService $userUpdateHandler, UserInterface $user)
    {
        // Get the data POST
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(UserUpdateType::class);

        // Submit the form
        $form->submit($data);

        // Update the user
        return $this->view($userUpdateHandler->handle($form, $user), Response::HTTP_OK);
    }

    /**
     * Update a user's password.
     *
     * @Rest\Patch(
     *      path="/api/users/password",
     *      name="users_update_password"
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
    public function updatePasswordAction(
        Request $request,
        UserPasswordHandlerService $userPasswordHandler,
        UserInterface $user
    ) {
        // Get the data POST
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(UserUpdatePasswordType::class);

        // Submit the form
        $form->submit($data);

        // Update the user
        return $this->view($userPasswordHandler->handle($form, $user), Response::HTTP_OK);
    }

    /**
     * Delete one user.
     *
     * @Rest\Delete(
     *      path="/api/users",
     *      name="users_delete",
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
    public function deleteAction(UserInterface $user, ObjectManager $entityManager)
    {
        // Remove the user
        $entityManager->remove($user);
        $entityManager->flush();

        return;
    }
}
