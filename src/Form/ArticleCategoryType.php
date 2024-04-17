<?php

namespace App\Form;

use App\Entity\Bloop;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('categories', EntityType::class, [
             'label'=>'Selectionnez vos catégories',
             'class' => Category::class,
             'query_builder' => function (CategoryRepository $categoryRepository): QueryBuilder {
                 return $categoryRepository->createQueryBuilder('u')
                     ->orderBy('u.name', 'ASC');
             },
             'choice_label' => 'name',
             'multiple' => true,
             'expanded' => true,
            'required'=>false,
         ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bloop::class,
        ]);
    }
}
