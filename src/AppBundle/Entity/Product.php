<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Product.
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "product_detail",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "product_list",
 *          absolute = true
 *      )
 * )
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer", type="string", length=255)
     */
    private $manufacturer;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock;

    /**
     * @var float
     *
     * @ORM\Column(name="TVA", type="float")
     */
    private $tVA;

    /**
     * @var float
     *
     * @ORM\Column(name="priceHT", type="float", nullable=true)
     */
    private $priceHT;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="AppBundle\Entity\Picture",
     *      mappedBy="product",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     */
    private $pictures;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set model.
     *
     * @param string $model
     *
     * @return Product
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set manufacturer.
     *
     * @param string $manufacturer
     *
     * @return Product
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer.
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set stock.
     *
     * @param int $stock
     *
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock.
     *
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set tVA.
     *
     * @param float $tVA
     *
     * @return Product
     */
    public function setTVA($tVA)
    {
        $this->tVA = $tVA;

        return $this;
    }

    /**
     * Get tVA.
     *
     * @return float
     */
    public function getTVA()
    {
        return $this->tVA;
    }

    /**
     * Set priceHT.
     *
     * @param float $priceHT
     *
     * @return Product
     */
    public function setPriceHT($priceHT)
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    /**
     * Get priceHT.
     *
     * @return float
     */
    public function getPriceHT()
    {
        return $this->priceHT;
    }

    /**
     * Return all pictures.
     *
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    /**
     * Add a picture.
     *
     * @param Picture $picture
     *
     * @return self
     */
    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setProduct($this);
        }

        return $this;
    }

    /**
     * Remove a picture.
     *
     * @param Picture $picture
     *
     * @return self
     */
    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getProduct() === $this) {
                $picture->setProduct(null);
            }
        }

        return $this;
    }
}
