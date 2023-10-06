<?php
namespace App\Entity;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\CartRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;    

    #[ORM\Column(type: 'integer')]
    private $quantity;  
    
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: "Cart")]
    private $cart;   

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: "Cart")]
    #[ORM\JoinColumn(nullable: false)]
    private $products;

    public function __construct()
    {
        $this->cart = new ArrayCollection();
        return $this->products;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getCart(): Collection
    {
        return $this->cart;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}