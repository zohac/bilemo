<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
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

    public function testUser()
    {
        $customer = $this->entityManager
                ->getRepository(Customer::class)
                ->findOneBy(['name' => 'Cyberdyne']);

        $user = new User();

        $user->setUsername('test');
        $user->setFirstname('test');
        $user->setLastname('test');
        $user->setEmail('test@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setCustomer($customer);
        $user->setPassword('1GreatPassword');
        $user->setSalt('AGreatSalt');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertTrue(is_numeric($user->getId()));
        $this->assertEquals('test', $user->getUsername());
        $this->assertEquals('test', $user->getFirstName());
        $this->assertEquals('test', $user->getLastname());
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertEquals('1GreatPassword', $user->getPassword());
        $this->assertEquals('AGreatSalt', $user->getSalt());
        $this->assertInstanceOf(Customer::class, $user->getCustomer());

        $this->entityManager->remove($user);
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
