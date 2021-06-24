<?php

namespace App\Controller\Purchase;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PurchaseRepository;
use App\Entity\Purchase;
use App\Cart\CartService;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/finish/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        // récupération de la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "Cette commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
        }
        //changer le status
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        //vider panier
        $cartService->empty();
        //rediriger vers la liste des commandes
        $this->addFlash('success', "La commande é bien été payée");
        return $this->redirectToRoute("purchase_index");
    }
}
