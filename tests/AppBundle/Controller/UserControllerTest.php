<?php

namespace tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // Mock the EntityManager to return the mock of the repository
        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Get a user in DB.
     *
     * @param string $username
     *
     * @return User
     */
    protected function getUser(string $username = 'test'): User
    {
        // Create new client
        $client = static::createClient();
        // Get the entityManager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        // Get a User
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        return $user;
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($username = 'user', $password = 'password')
    {
        // Create a client
        $client = static::createClient();
        // Send username and password
        $client->request(
            'POST',
            '/api/login_check',
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );
        // Decode json data to retrieve the token
        $data = json_decode($client->getResponse()->getContent(), true);

        // Erase client with new
        $client = static::createClient();
        // Send data with
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        // Return the authenticated client
        return $client;
    }

    /**
     * test ListAction with token.
     */
    public function testGetListActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('sjouan', '1GreatP@ssword');
        // Test the route
        $client->request('GET', '/api/users');
        // Check the response
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test ListAction without token.
     */
    public function testGetListActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Test the route
        $client->request('GET', '/api/users');
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction with token.
     */
    public function testGetDetailActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('sjouan', '1GreatP@ssword');
        // Retrieves a user from the database
        $user = $this->getUser('sjouan');
        // Test the route
        $client->request('GET', '/api/users/'.$user->getId());
        // Check the response
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction without token.
     */
    public function testGetDetailActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Retrieves a user from the database
        $user = $this->getUser('sjouan');
        // Test the route
        $client->request('GET', '/api/users/'.$user->getId());
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test CreateAction with token.
     */
    public function testPostCreateActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('sjouan', '1GreatP@ssword');
        // Test the route with post data
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'test',
                    'firstname' => 'test',
                    'lastname' => 'test',
                    'email' => 'test@test.com',
                    'password' => '1GreatP@ssword',
                ]
            )
        );
        // Check the response
        //\var_dump($client->getResponse()); die;
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    /**
     * test CreateAction without token.
     */
    public function testPostCreateActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Test the route with post data
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'test',
                    'firstname' => 'test',
                    'lastname' => 'test',
                    'email' => 'test@test.com',
                    'password' => '1GreatPassword',
                ]
            )
        );
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test CreateAction with token and generates an exception.
     */
    public function testPostCreateActionWithException()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('sjouan', '1GreatP@ssword');
        // Test the route with post data
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'test',
                ]
            )
        );
        // Check the response
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    /**
     * test UpdateAction with token.
     */
    public function testUpdateActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('sjouan', '1GreatP@ssword');
        // Retrieves a user from the database
        $user = $this->getUser('test');
        // Test the route with patch data
        $client->request(
            'PATCH',
            '/api/users/'.$user->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'firstname' => 'test2',
                    'lastname' => 'test2',
                    'email' => 'test2@test.com',
                ]
            )
        );
        // Check the response
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test UpdateAction without token.
     */
    public function testUpdateActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Retrieves a user from the database
        $user = $this->getUser('test');
        // Test the route with patch data
        $client->request(
            'PATCH',
            '/api/users/'.$user->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'password' => '1GreatPassword',
                ]
            )
        );
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test UpdatePasswordAction with token.
     */
    public function testUpdatePasswordActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('test', '1GreatP@ssword');
        // Test the route with patch data
        $client->request(
            'PATCH',
            '/api/users/password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'password' => 'AnotherGr3atP@ssword',
                ]
            )
        );
        // Check the response
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test UpdatePasswordAction without token.
     */
    public function testUpdatePasswordActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Test the route with patch data
        $client->request(
            'PATCH',
            '/api/users/password',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'password' => '1GreatPassword',
                ]
            )
        );
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DeleteAction without token.
     */
    public function testDeleteActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Retrieves a user from the database
        $user = $this->getUser('test');
        // Test the route to delete a user
        $client->request('DELETE', '/api/users');
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DeleteAction with token.
     */
    public function testDeleteActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('test', 'AnotherGr3atP@ssword');
        // Retrieves a user from the database
        $user = $this->getUser('test');
        // Test the route to delete a user
        $client->request('DELETE', '/api/users');
        // Check the response
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->entityManager = null;
        $this->user = null;
    }
}
