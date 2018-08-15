<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Controller managing the products.
 */
class ProductController extends FOSRestController
{
    /**
     * Get the list of all product.
     *
     * @Rest\Get(
     *      path="/api/products",
     *      name="product_list"
     * )
     *
     * @Rest\View(StatusCode = 200)
     */
    public function listAction(ObjectManager $entityManager)
    {
        return $entityManager->getRepository(Product::class)->findAllWhithAllEntities();
    }
}
