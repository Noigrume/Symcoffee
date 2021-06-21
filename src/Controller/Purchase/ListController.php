<?php

namespace App\Controller\Purchase;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Veuillez pour connecter pour accéder à cette section")
     */
    public function index()
    {
        $user = $this->getUser();
        //Envoyer l'utilisateur connecté à twig
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}