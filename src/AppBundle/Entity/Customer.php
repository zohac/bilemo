<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Customer.
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 */
class Customer
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
     *
     * @Serializer\Groups({"customer"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Serializer\Groups({"customer"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255)
     *
     * @Serializer\SerializedName("phoneNumber")
     * @Serializer\Groups({"customer"})
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     *
     * @Serializer\Groups({"customer"})
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="postalCode", type="integer")
     *
     * @Serializer\SerializedName("postalCode")
     * @Serializer\Groups({"customer"})
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     *
     * @Serializer\Groups({"customer"})
     */
    private $country;

    /**
     * @var User|null
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="customer", orphanRemoval=true)
     *
     * @Serializer\Exclude()
     */
    private $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Customer
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
     * Set email.
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phoneNumber.
     *
     * @param string $phoneNumber
     *
     * @return Customer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Customer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postalCode.
     *
     * @param int $postalCode
     *
     * @return Customer
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode.
     *
     * @return int
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return Customer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get an user.
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add a user.
     *
     * @param User $user
     *
     * @return self
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCustomer($this);
        }

        return $this;
    }

    /**
     * Remove a user.
     *
     * @param User $user
     *
     * @return self
     */
    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCustomer() === $this) {
                $user->setCustomer(null);
            }
        }

        return $this;
    }
}
