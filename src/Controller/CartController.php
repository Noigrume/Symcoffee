<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Cart\CartService;
use App\Form\CartConfirmationType;


class CartController extends AbstractController
{

    protected $cartService;
    protected $productRepository;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     * Ajoute un produit au panier
     */
    public function add($id, Request $request)
    {
        //récupération du produit
        $product = $this->productRepository->find($id);

        //S'il n'existe pas on lance une exception
        if (!$product) {
            throw $this->createNotFoundException(" Ce produit n'existe pas");
        }

        //S'il existe on l'ajoute au panier
        $this->cartService->add($id);

        //Ajout d'un message flash de succès
        $this->addFlash('success', "le produit a bien été ajouté");

        //get: y'a t'il une info nous indiquant de rester sur le panier ?
        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }

        //Rediriger sur la fiche produit
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }


    /**
     * @Route("/cart", name="cart_show")
     * Affiche les produits du panier
     */
    public function show(CartService $cartService)
    {
        $form = $this->createForm(CartConfirmationType::class);
        //récupération du panier
        $finalCart = $cartService->getCartitems();

        //récupération du prix total
        $total = $cartService->getTotal();

        //Envoie des données au template
        return $this->render('cart/index.html.twig', [
            'cart' => $finalCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
     * Supprime complètement un produit du panier
     */
    public function delete($id)
    {
        //récupération du produit
        $product = $this->productRepository->find($id);

        //S'il n'existe pas on lance une exception
        if (!$product) {
            throw $this->createNotFoundException("Ce café n'existe pas et ne peut pas être supprimé");
        }
        //S'il existe on le supprime
        $this->cartService->remove($id);

        //Ajout d'un message flash de succès
        $this->addFlash("succes", "Le produit a bien été retiré du panier");

        //On redirige vers le panier
        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
     * Diminue le nombre d'un produit dans le panier
     */
    public function decrement($id)
    {
        //récupération du produit
        $product = $this->productRepository->find($id);

        //S'il n'existe pas on lance une exception
        if (!$product) {
            throw $this->createNotFoundException("Ce café n'existe pas et ne peut pas être supprimé");
        }
        //S'il existe on le décrémente
        $this->cartService->decrement($id);

        //On redirige vers le panier
        return $this->redirectToRoute("cart_show");
    }
}
