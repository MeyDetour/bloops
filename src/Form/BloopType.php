<?php

namespace App\Form;

use App\Entity\Bloop;
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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BloopType extends AbstractType
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

            ->add('chapo', TextareaType::class, [
                'label' => "Inscrivez une description à votre bloop.",
                'required' => true, // Champ non requis

            ])

            /*       ->add('categories', ChoiceType::class, [

                       'choices' => $categories,
       'choice_label'=>'name'
                   ])*/
            ->add('displayComments', CheckboxType::class, [
                'label' => 'Activez les commentaires',
                'required' => false, // Champ non requis
                "data"=>true
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bloop::class,
        ]);
    }
}
