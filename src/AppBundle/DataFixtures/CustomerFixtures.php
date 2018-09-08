<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Yaml;

class CustomerFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * Load fixture.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $customers = Yaml::parseFile('src/AppBundle/DataFixtures/Customer.yml');

        foreach ($customers as $customerData) {
            $customer = new Customer();

            $customer->setName($customerData['name']);
            $customer->setEmail($customerData['email']);
            $customer->setPhoneNumber($customerData['phoneNumber']);
            $customer->setAddress($customerData['address']);
            $customer->setPostalCode($customerData['postalCode']);
            $customer->setCountry($customerData['country']);

            $manager->persist($customer);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
