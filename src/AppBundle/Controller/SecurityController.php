<?php

namespace AppBundle\Controller;

use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class managing the authentication.
 */
class SecurityController extends FOSRestController
{
    /**
     * Authentication client and get access token.
     *
     * @Rest\Post(
     *      path="/api/login_check",
     *      name="login_check"
     * )
     *
     * @SWG\Post(
     *     description="Authentication client and get access token.",
     *     tags = {"Authentication"},
     *     @SWG\Response(
     *         response="200",
     *         description="Successful operation",
     *         @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="token", type="string"),
     *              ),
     *          )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: Method Not Allowed",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Bad credentials",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not Found: Invalid Route",
     *     ),
     *     @SWG\Parameter(
     *          name="Body",
     *          required= true,
     *          in="body",
     *          type="string",
     *          description="Use as login '_username: username, _password: password'",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="_username", type="string"),
     *                  @SWG\Property(property="_password", type="string"),
     *              ),
     *          )
     *      )
     * )
     */
    public function login()
    {
        //manage by lexik_jwt_authentication
    }
}
