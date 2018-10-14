<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use AppBundle\Entity\Customer;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Liste des noms de catégorie à ajouter
        $users = Yaml::parseFile('src/AppBundle/DataFixtures/User.yml');

        foreach ($users as $userData) {
            $customer = $manager
                ->getRepository(Customer::class)
                ->findOneBy(['name' => $userData['customer']]);

            $user = new User();

            $user->setUsername($userData['username']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setCustomer($customer);

            $password = $this->encoder->encodePassword($user, $userData['password']);
            $user->setPassword($password);

            // On la persiste
            $manager->persist($user);
        }

        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
