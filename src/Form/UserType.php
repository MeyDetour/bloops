<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\UX\Dropzone\Form\DropzoneType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class)
           ->add('username',TextType::class)
            ->add('description',TextAreaType::class, [
                'constraints' => [
                    new Length([
                        'max' => 400,
                        'maxMessage' => 'Votre description ne peut pas dépasser 400 caractères.',
                    ]),
                ],
                'attr' => [
                    'maxlength' => 400, // Attribut HTML pour limiter la saisie
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
