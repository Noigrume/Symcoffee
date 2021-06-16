<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'nom du produit',
                'attr' => ['placeholder' => 'tapez le nom du produit']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'description courte',
                'attr' => ['placeholder' => 'tapez une description courte']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'prix du produit en ',
                'attr' => ['placeholder' => 'tapez le prix du produit'],
                'divisor' => 100
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'image du produit',
                'attr' => ['placeholder' => 'tapez une url']
            ])
            ->add('category', EntityType::class, [
                'label' => 'catégorie',

                'placeholder' => '-- Choisir une catégorie',
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);

        //$builder->get('price')->addModelTransformer(new CentimesTransformer);

//        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
//
//            $product = $event->getData();
//
//            if ($product->getPrice() !== null) {
//                $product->setPrice($product->getPrice() * 100);
//            }
//        });
//
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $form = $event->getForm();
//            /** @var Product $product */
//            $product = $event->getData();
//
//            if ($product->getPrice() !== null) {
//                $product->setPrice($product->getPrice() / 100);
//            }

        /* if ($product->getId() === null) {

             $form->add('category', EntityType::class, [
                 'label' => 'catégorie',

                 'placeholder' => '-- Choisir une catégorie',
                 'class' => Category::class,
                 'choice_label' => function (Category $category) {
                     return strtoupper($category->getName());
                 }
             ]);
         }*/
        //});

    }

    public
    function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
