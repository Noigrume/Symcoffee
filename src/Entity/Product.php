<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le nom est obligatoire")
     * @Assert\Length(min=3, max=255, minMessage="le nom doit contenir au moins 3 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="le prix est obligatoire")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(message="la photo doit être une url valide")
     */
    private $mainPicture;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="la description est obligatoire")
     * @Assert\Length(min=20, minMessage="la description courte doit avoir au moins 20 caractères")
     */
    private $shortDescription;

    /**
     * @ORM\OneToMany(targetEntity=Purchase::class, mappedBy="product")
     */
    private $purchase;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="product")
     */
    private $purchaseItems;

    /**
     * @ORM\ManyToMany(targetEntity=Purchase::class, mappedBy="products")
     */
    private $purchases;



    public function __construct()
    {
        $this->purchase = new ArrayCollection();
        $this->purchaseItems = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->purchaseItems = new ArrayCollection();
    }

//    public static function loadValidatorMetadata(ClassMetadata $metadata)
//    {
//        $metadata->addPropertyConstraints('name', [
//            new Assert\NotBlank(['message' => 'le nom est obligatoire']),
//            new Assert\Length(['min' => 3, 'max' => 255, 'minMessage' => 'le nom doit contenir au moins 3 caractères'])
//        ]);
//
//        $metadata->addPropertyConstraints('price', new Assert\NotBlank(['message' => 'le prix est obligatoire']));
//
//    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(string $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchase(): Collection
    {
        return $this->purchase;
    }

    public function addPurchase(PurchaseItem $purchase): self
    {
        if (!$this->purchase->contains($purchase)) {
            $this->purchase[] = $purchase;
            $purchase->setProduct($this);
        }

        return $this;
    }

    public function removePurchase(PurchaseItem $purchase): self
    {
        if ($this->purchase->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getProduct() === $this) {
                $purchase->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getProduct() === $this) {
                $purchaseItem->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItem(): Collection
    {
        return $this->purchaseItem;
    }


}
