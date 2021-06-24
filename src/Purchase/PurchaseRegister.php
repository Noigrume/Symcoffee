<?php

namespace App\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\DateTime;
use App\Cart\CartService;
use Symfony\Component\Security\Core\Security;

class PurchaseRegister
{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase)
    {

        // on li la purchase à l’utilisateur connecté
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new \DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        // on a lie avec les produits qui sont dans le panier( cart service)
        foreach ($this->cartService->getCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->quantity)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());
            $this->em->persist($purchaseItem);
        }
        $this->em->flush();
    }
}