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
use App\Purchase\PurchaseRegister;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;
    protected $register;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchaseRegister $register)
    {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->register = $register;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous ne pouvez pas confirmer une commande sans être connecté")
     */
    public function confirm(Request $request)
    {
        // lire les données du formulaire
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // si le formulaire n’a pas été soumis on sort
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Veuillez remplir le formulaire avant confirmation');
            return $this->redirectToRoute('cart_show');
        }

        // s’il n’y a pas de produit dans le panier, je sors
        $cartItems = $this->cartService->getCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer un panier vide');
            return $this->redirectToRoute('cart_show');
        }
        // extrait la commande du formulaire
        /** @var  $Purchase */
        $purchase = $form->getData();


        //je demande à l'enregistrer
        $this->register->storePurchase($purchase);

        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}