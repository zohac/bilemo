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

        // Last, mock the EntityManager to return the mock of the repository
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
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
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
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(
                '_username' => $username,
                '_password' => $password,
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    /**
     * test ListAction with token.
     */
    public function testGetListActionWithToken()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $client->request('GET', '/api/users');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test ListAction without token.
     */
    public function testGetListActionWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction with token.
     */
    public function testGetDetailActionWithToken()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $user = $this->getUser('sjouan');
        $client->request('GET', '/api/users/'.$user->getId());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction without token.
     */
    public function testGetDetailActionWithoutToken()
    {
        $client = static::createClient();
        $user = $this->getUser('sjouan');
        $client->request('GET', '/api/users/'.$user->getId());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test CreateAction with token.
     */
    public function testPostCreateActionWithToken()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'test2',
                    'firstname' => 'test2',
                    'lastname' => 'test2',
                    'email' => 'test2@test.com',
                    'password' => '1GreatPassword',
                ]
            )
        );

        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    /**
     * test CreateAction without token.
     */
    public function testPostCreateActionWithoutToken()
    {
        $client = static::createClient();
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

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test CreateAction with token and generates an exception.
     */
    public function testPostCreateActionWithException()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'test3',
                ]
            )
        );

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
    }

    /**
     * test UpdateAction with token.
     */
    public function testUpdateActionWithToken()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $user = $this->getUser('test2');
        $client->request(
            'PUT',
            '/api/users/'.$user->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'username' => 'test',
                    'firstname' => 'test',
                    'lastname' => 'test',
                    'email' => 'test@test.com',
                    'password' => 'A1GreatPassword',
                ]
            )
        );

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test UpdateAction without token.
     */
    public function testUpdateActionWithoutToken()
    {
        $client = static::createClient();
        $user = $this->getUser('test');
        $client->request(
            'PUT',
            '/api/users/'.$user->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'firstname' => 'test2',
                    'lastname' => 'test2',
                    'email' => 'test2@test.com',
                    'password' => 'A1GreatPassword',
                ]
            )
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DeleteAction with token.
     */
    public function testDeleteActionWithToken()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $user = $this->getUser('test');
        $client->request('DELETE', '/api/users/'.$user->getId());

        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * test DeleteAction without token.
     */
    public function testDeleteActionWithoutToken()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/users/1');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
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
