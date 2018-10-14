<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Product;
use AppBundle\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Yaml;

class ProductFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $products = Yaml::parseFile('src/AppBundle/DataFixtures/Product.yml');

        foreach ($products as $productData) {
            $product = new Product();

            $product->setName($productData['name']);
            $product->setModel($productData['model']);
            $product->setDescription($productData['description']);
            $product->setManufacturer($productData['manufacturer']);
            $product->setStock($productData['stock']);
            $product->setTVA($productData['TVA']);
            $product->setPriceHT($productData['priceHT']);

            foreach ($productData['pictures'] as $path) {
                $picture = new Picture();
                $picture->setName(uniqid());
                $picture->setPath($path);
                $product->addPicture($picture);
            }
            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
