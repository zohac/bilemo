<?php

namespace tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client = null;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    /**
     * Test the login.
     *
     * @see SecurityController::login()
     */
    public function testLoginPass()
    {
        // Make the request
        $this->client->request(
            'POST',
            '/api/login_check',
            array(
                '_username' => 'sjouan',
                '_password' => '1GreatP@ssword',
            )
        );
        // Check the content body
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        // Check the statut code
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Check if the response is successful
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Test the login.
     *
     * @see SecurityController::login()
     */
    public function testLoginFail()
    {
        // Make the request
        $this->client->request(
            'POST',
            '/api/login_check',
            [
                '_username' => 'BadUsername',
                '_password' => 'BadPassword',
            ]
        );
        // Check the statut code
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        // Check if the response is successful
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->client = null;
    }
}
