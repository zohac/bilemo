<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $client = static::createClient();
        $this->entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testCustomer()
    {
        $customer = new Customer();

        $customer->setName('A Great Name');
        $customer->setEmail('test@test.com');
        $customer->setPhoneNumber(0101010101);
        $customer->setAddress('A Great Address');
        $customer->setPostalCode(50700);
        $customer->setCountry('France');

        $user = new User();
        $customer->addUser($user);

        $this->assertEquals('A Great Name', $customer->getName());
        $this->assertEquals('test@test.com', $customer->getEmail());
        $this->assertEquals(0101010101, $customer->getPhoneNumber());
        $this->assertEquals('A Great Address', $customer->getAddress());
        $this->assertEquals(50700, $customer->getPostalCode());
        $this->assertEquals('France', $customer->getCountry());
        $this->assertContains($user, $customer->getUsers());

        $customer->removeUser($user);
        $this->assertNotContains($user, $customer->getUsers());
        $this->assertEmpty($customer->getUsers());

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $this->assertTrue(is_numeric($customer->getId()));

        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
