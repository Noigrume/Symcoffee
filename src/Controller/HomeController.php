<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class HomeController extends AbstractController
{
    /**
     *
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository)
    {
        $product = $productRepository->findBy([], [], 3);
        return $this->render('home.html.twig', [
            'products' => $product
        ]);
    }
}