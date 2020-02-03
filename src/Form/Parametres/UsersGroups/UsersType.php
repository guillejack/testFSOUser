<?php

namespace App\Form\Parametres\UsersGroups;

use Symfony\Component\Form\AbstractType;
use App\Entity\Parametres\UsersGroups\Users;
use App\Entity\Parametres\UsersGroups\Groups;
use App\Entity\Parametres\UsersGroups\Services;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('nom')
            ->add('prenom')
            ->add('DDN')
            ->add('email')
            ->add('tel_interne')
            ->add('tel_portable')
            ->add('photo')
            ->add('is_active')
            ->add('creationDate')
            ->add('service' , EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name',
                 
            ])
            ->add('groupes', EntityType::class, [
                'class' => Groups::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
