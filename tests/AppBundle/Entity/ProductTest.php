<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\Picture;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductTest extends WebTestCase
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

    public function testProduct()
    {
        $product = new Product();

        $product->setName('A Great Name');
        $product->setModel('A super model');
        $product->setDescription('A great description!');
        $product->setManufacturer('test');
        $product->setStock(100);
        $product->setTVA(20.0);
        $product->setPriceHT(999.99);

        $picture = new Picture();
        $picture->setName('AGoodNameForAPicture');
        $picture->setPath('A\Great\Path');

        $product->addPicture($picture);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->assertTrue(is_numeric($product->getId()));
        $this->assertEquals('A Great Name', $product->getName());
        $this->assertEquals('A super model', $product->getModel());
        $this->assertEquals('A great description!', $product->getDescription());
        $this->assertEquals('test', $product->getManufacturer());
        $this->assertEquals(100, $product->getStock());
        $this->assertEquals(20.0, $product->getTVA());
        $this->assertEquals(999.99, $product->getPriceHT());
        $this->assertContains($picture, $product->getPictures());

        foreach ($product->getPictures() as $picture) {
            $this->assertTrue(is_numeric($picture->getId()));
            $this->assertEquals('AGoodNameForAPicture', $picture->getName());
            $this->assertEquals('A\Great\Path', $picture->getPath());
        }

        $product->removePicture($picture);
        $this->assertNotContains($picture, $product->getPictures());

        $this->entityManager->remove($product);
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
