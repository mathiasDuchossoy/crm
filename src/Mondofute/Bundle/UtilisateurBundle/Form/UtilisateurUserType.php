<?php

namespace Mondofute\Bundle\UtilisateurBundle\Form;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utilisateur', UtilisateurType::class, array(
                'data_class' => Utilisateur::class
            ));
//            ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
//            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            // vérifie si l'objet Product est "nouveau"
            // Si aucune donnée n'est passée au formulaire, la donnée est "null".
            // Ce doit être considéré comme un nouveau "Product"
            if ($user && null !== $user->getId()) {
                $form
                    ->add('plainPassword', PasswordType::class, array(
                        'translation_domain' => 'FOSUserBundle',
                        'label' => 'form.new_password',
                        'required' => false,
                        'attr' => [
                            'class' => 'password'
                        ]
                    ));
            } else {
                $form
                    ->add('plainPassword', PasswordType::class, array(
                        'translation_domain' => 'FOSUserBundle',
                        'label' => 'form.password',
                        'attr' => [
                            'class' => 'password'
                        ]
                    ));

            }
        });

    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\UtilisateurBundle\Entity\UtilisateurUser'
        ));
    }

    public function getName()
    {
        return 'mondofute_utilisateur_user';
    }


}
