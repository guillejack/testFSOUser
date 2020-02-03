<?php

namespace App\Form\Parametres\UsersGroups;

use Symfony\Component\Form\AbstractType;
use App\Entity\Parametres\UsersGroups\Users;
use App\Entity\Parametres\UsersGroups\Groups;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Parametres\UsersGroups\UsersRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class GroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$em = $this->getDoctrine()->getEntityManager();

        $form =  $builder->getData();
        $builder
            ->add('role')
            ->add('name')
            ->add('description')
            /*->add('users', EntityType::class, [
              'class' => Users::class,                
               'choice_label' => 'username',
               'query_builder' => function (UsersRepository $repo) {
                    return $repo->createQueryBuilder('f')
                        ->where('f.id > :id')
                       ->setParameter('id', 1);
                },
                'multiple' => true,
           ])*/
          ->add('users',  EntityType::class, [
            'class' => Users::class,         
            'choice_label' => 'username',
            'multiple' => true,
            'expanded'=> true,

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
