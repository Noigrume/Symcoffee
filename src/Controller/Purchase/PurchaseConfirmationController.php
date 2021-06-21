<?php

namespace App\Controller\Purchase;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Entity\PurchaseItem;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }


    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous ne pouvez pas confirmer une commande sans être connecté")
     */
    public function confirm(Request $request)
    {
        // lire les données du formulaire
        // formfactory interface + request
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // si le formulaire n’a pas été soumis on sort
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Veuillez remplir le formulaire avant confirmation');
            return $this->redirectToRoute('cart_show');
        }
        // si je ne suis pas co on sort (security)
        $user = $this->getUser();

        // s’il n’y a pas de produit dans le panier, je sors
        $cartItems = $this->cartService->getCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer un panier vide');
            return $this->redirectToRoute('cart_show');
        }
        // sinon on crée une purchase
        /** @var  $purchase */
        $purchase = $form->getData();
        // on li la purchase à l’utilisateur connecté
        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
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

        // enregistrer la commander (entityManager)
        $this->em->flush();

        $this->cartService->empty();
        $this->addFlash('success', "La commande a été enregistrée avec succès");
        return $this->redirectToRoute('purchase_index');
    }
}