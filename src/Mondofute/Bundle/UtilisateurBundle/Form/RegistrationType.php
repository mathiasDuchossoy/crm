<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 03/05/2016
 * Time: 11:03
 */
// src/Mondofute\Bundle\UtilisateurBundle\Form/RegistrationType.php

namespace Mondofute\Bundle\UtilisateurBundle\Form;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utilisateur',
                UtilisateurType::class,
                array(
                    'data_class' => Utilisateur::class
                ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    // For Symfony 2.x

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}