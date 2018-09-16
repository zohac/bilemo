<?php

namespace tests\AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    /**
     * @var Product
     */
    private $product;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $client = static::createClient();
        // Get entityManager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        // Get one product
        $this->product = $entityManager->getRepository(Product::class)->findOneBy(['name' => 'Dok Phone razer']);
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
        $client = $this->createAuthenticatedClient('sjouan', '1');
        // Test the route
        $client->request('GET', '/api/products');

        // Check the response
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        // asserts that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
    }

    /**
     * test ListAction without token.
     */
    public function testGetListActionWithoutToken()
    {
        // Create an un-authenticated client
        $client = static::createClient();
        // Test the route
        $client->request('GET', '/api/products');
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction with token.
     */
    public function testGetDetailActionWithToken()
    {
        // Get an authenticated client
        $client = $this->createAuthenticatedClient('sjouan', '1');
        // Test the route
        $client->request('GET', '/api/products/'.$this->product->getId());
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
        // Test the route
        $client->request('GET', '/api/products/'.$this->product->getId());
        // Check the response
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->product = null; // avoid memory leaks
    }
}
