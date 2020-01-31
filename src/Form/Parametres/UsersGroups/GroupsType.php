<?php

namespace App\Form\Parametres\UsersGroups;

use Symfony\Component\Form\AbstractType;
use App\Entity\Parametres\UsersGroups\Users;
use App\Entity\Parametres\UsersGroups\Groups;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles')
            ->add('name')
            ->add('description')
            ->add('users', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'username',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Groups::class,
        ]);
    }
}
