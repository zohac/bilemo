<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Swagger\Annotations as SWG;
use AppBundle\Form\User\CreateType;
use AppBundle\Utils\UpdateUserHandler;
use AppBundle\Utils\User\CreateHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Exception\ResourceValidationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolationList;
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
        $data = json_decode($request->getContent(), true);

        // Build the form
        $form = $this->createForm(CreateType::class);

        // Submit the form
        $form->submit($data);

        // Return the user
        return $handler->handle($form);
    }

    /**
     * @Rest\Put(
     *      path="/api/users/{id}",
     *      name="users_update",
     *      requirements = {"id"="\d+"}
     * )
     * @ParamConverter("userUpdate", converter="fos_rest.request_body")
     * @ParamConverter("user",  options={"mapping"={"id"="id"}})
     *
     * @Rest\View(StatusCode = 200)
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @SWG\Put(
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
    public function update(
        User $user,
        User $userUpdate,
        UpdateUserHandler $handler,
        ConstraintViolationList $violations = null
    ) {
        // Check the contraint in user entity
        if (count($violations)) {
            throw new ResourceValidationException($violations);
        }
        // Update the user
        return $handler->update($userUpdate, $user);
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
