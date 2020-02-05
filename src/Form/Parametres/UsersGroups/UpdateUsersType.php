<?php

namespace App\Form\Parametres\UsersGroups;

use Symfony\Component\Form\AbstractType;
use App\Entity\Parametres\UsersGroups\Users;
use App\Entity\Parametres\UsersGroups\Groups;
use App\Entity\Parametres\UsersGroups\Services;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UpdateUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('nom')
            ->add('prenom')
            ->add('DDN',DateType::class , array(
                'html5' => false,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                ))
            ->add('email', EmailType::class , [
                'required' => false                
            ])
            ->add('tel_interne', TelType::class, [
                'required' => false                
            ])
            ->add('tel_portable', TelType::class, [
                'required' => false                
            ])
            ->add('is_active', CheckboxType::class, [
                'required' => false,
                'label'    => "Actif"                
            ])
            ->add('photo', HiddenType::class, array(
                'required' => false,
              ))
            ->add('service' , EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name',
                'required' => false
                 
            ])
            ->add('groupes', EntityType::class, [
                'class' => Groups::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'expanded'=> true,
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
