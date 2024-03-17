<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();


        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'article",
                'required' => true, // Champ non requis

            ])
            ->add('chapo', TextType::class, [
                'label' => "Chapô de l'article",
                'required' => false, // Champ non requis

            ])->add('first_subtitle', TextType::class, [
                'label' => "Sous titre",
                'required' => false, // Champ non requis

            ])
            ->add('first_paragraphe', TextType::class, [
                'label' => "Paragraphe",
                'required' => false, // Champ non requis

            ])
            ->add('second_subtitle', TextType::class, [
                'label' => "Sous titre",
                'required' => false, // Champ non requis

            ])
            ->add('second_paragraphe', TextType::class, [
                'label' => "Paragraphe",
                'required' => false, // Champ non requis

            ])
            ->add('third_subtitle', TextType::class, [
                'label' => "Sous titre",
                'required' => false, // Champ non requis

            ])
            ->add('third_paragraphe', TextType::class, [
                'label' => "Paragraphe",
                'required' => false, // Champ non requis

            ])
            ->add('note',TextType::class,[
                'label'=>"Note de l'auteur",
                  'required' => false, // Champ non requis

            ])
            /*       ->add('categories', ChoiceType::class, [

                       'choices' => $categories,
       'choice_label'=>'name'
                   ])*/
            ->add('displayComments', CheckboxType::class, [
                'label' => 'Activez les commentaires',
                'required' => false, // Champ non requis
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
