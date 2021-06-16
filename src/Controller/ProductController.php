<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\ProductType;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;


use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, categoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$category) {
            throw $this->createNotFoundException("la catégorie n'existe pas");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }


    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, productRepository $productRepository): Response
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$product) {
            throw $this->createNotFoundException("le produit n'existe pas");
        }
        return $this->render('product/show.html.twig', [

            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {


        $product = $productRepository->find($id);

        $form = $this->createForm(productType::class, $product);

        //$form->setData($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //getDatta optionnel, car le formulaire travail sur l'objet product
            //$product = $form->getData();
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }


    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public
    function create(FormFactoryInterface $formFactoryInterface, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);

        }

        $formView = $form->createView();
        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
