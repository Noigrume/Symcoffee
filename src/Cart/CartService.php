<?php

namespace App\Cart;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use App\Cart\CartItem;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    //récupère le panier en session
    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    //enregistre le panier en session
    protected function saveCart(array $cart)
    {
        return $this->session->set('cart', $cart);
    }

    //vider le panier
    public function empty()
    {
        $this->saveCart([]);
    }

    //Ajout d'un item au panier
    public function add(int $id)
    {
        //récupération du panier
        $cart = $this->getCart();

        //Test si le produit existe en session
        if (!array_key_exists($id, $cart)) {

            $cart[$id] = 0;
        }
        $cart[$id]++;

        //enregistrement du panier
        $this->saveCart($cart);
    }

    //Supprime entièrement le produit du panier
    public function remove($id)
    {

        //récupère le panier
        $cart = $this->getCart();
        //suppression du l'article demandé
        unset($cart[$id]);
        //Actualisation en session
        $this->saveCart($cart);
    }


    //Diminue de 1 la quantité de l'article dans le panier
    public function decrement(int $id)
    {
        //récupèration du panier
        $cart = $this->getCart();
        // vérifie si l'article existe en session
        if (!array_key_exists($id, $cart)) {
            //s'il n'existe pas on ne fait rien
            return;
        }
        //si le produit est unique, on le supprime
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }
        //plusieurs produits, on décrémente
        $cart[$id]--;
        $this->$this->getCart();
    }


    //Obtient le prix total du panier
    public function getTotal(): int
    {
        //initiation à 0
        $total = 0;

        //pour chaque article du panier
        foreach ($this->getCart() as $id => $quantity) {
            //on récupère les info du produit
            $product = $this->productRepository->find($id);

            //si le produit n'existe pas on poursuit la boucle
            if (!$product) {
                continue;
            }
            //on ajoute au total le prix * la quantité du produit
            $total += ($product->getPrice() * $quantity);
        }
        return $total;
    }

    //Récupère les éléments stockés dans le panier
    public function getCartItems(): array
    {
        //initiation du panier
        $finalCart = [];

        //Boucle sur les produits en session
        foreach ($this->getCart() as $id => $quantity) {
            //récupération du produit avec le repository
            $product = $this->productRepository->find($id);

            //Si le produit n'existe pas on poursuit la boucle
            if (!$product) {
                continue;
            }

            //Ajoute au panier notre produit et sa quantité
            $finalCart[] = new CartItem($product, $quantity);
        }
        return $finalCart;
    }

}