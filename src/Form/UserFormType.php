<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Lessons;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'E-mail'
            ])
            ->add('name', null, [
                'label' => 'Nom de l\'utilisateur'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles'
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('purchasedCursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Cursus achetés'
            ])
            ->add('purchasedLessons', EntityType::class, [
                'class' => Lessons::class,
                'choice_label' => function ($lesson) {
                    return $lesson->getName() . ' - ' . $lesson->getCursus()->getName();
                },
                'multiple' => true,
                'expanded' => false,
                'label' => 'Leçons achetées'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
