<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;

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
                'attr' => ['placeholder' => 'tapez le prix du produit']
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

            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
