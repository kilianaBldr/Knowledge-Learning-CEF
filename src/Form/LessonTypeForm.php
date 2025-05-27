<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Lessons;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType; 
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'name',
                'label' => 'Cusus Associé'
            ])
            ->add('description')
            ->add('videoFile', FileType::class, [
                'label' => 'Fichier Vidéo',
                'mapped' =>false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['video/mp4'],
                        'mimeTypesMessage' => 'Merci de télécharger un fichier vidéo MP4 valide.'
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu Complet',
                'required' => false,
            ])
            ->add('price')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lessons::class,
        ]);
    }
}
