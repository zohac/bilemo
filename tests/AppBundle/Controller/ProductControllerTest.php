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
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
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
        $client->request('GET', '/api/products');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        // asserts that the response status code is 2xx
        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
    }

    /**
     * test ListAction without token.
     */
    public function testGetListActionWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction with token.
     */
    public function testGetDetailActionWithToken()
    {
        $client = $this->createAuthenticatedClient('sjouan', '1');
        $client->request('GET', '/api/products/'.$this->product->getId());

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * test DetailAction without token.
     */
    public function testGetDetailActionWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/'.$this->product->getId());

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
