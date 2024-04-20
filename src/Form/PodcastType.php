<?php

namespace App\Form;

use App\Entity\Audio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class PodcastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => "Titre du podcast"
            ])
            ->add('description', TextareaType::class, [
                'label' => "Contenu du podcast",
                'required' => false, // Champ non requis
            ])
            ->add('displayComment', CheckboxType::class, [
                'label' => 'Activez les commentaires',
                'required' => false, // Champ non requis
                "data"=>true
            ])
            ->add('audioFile', DropzoneType::class, [
                'label'=>'Fichiers valid (MP3, OGG, WAV)' ,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner un fichier podcast',
                        ]),

                        new File([
                            'mimeTypes' => [
                                'audio/mpeg',
                                'audio/ogg',
                                'audio/wav',
                                'audio/webm',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid audio file (MP3, OGG, WAV)',
                        ]),

                    ]]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Audio::class,
        ]);
    }
}
