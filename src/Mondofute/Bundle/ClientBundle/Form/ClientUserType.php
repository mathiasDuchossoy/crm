<?php

namespace Mondofute\Bundle\ClientBundle\Form;

use FOS\UserBundle\Util\LegacyFormHelper;
use Mondofute\Bundle\ClientBundle\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', ClientType::class, array(
                'data_class' => Client::class
            ));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            // vérifie si l'objet Product est "nouveau"
            // Si aucune donnée n'est passée au formulaire, la donnée est "null".
            // Ce doit être considéré comme un nouveau "Product"
            if ($user && null !== $user->getId()) {
                $form
                    ->add('plainPassword', PasswordType::class, array(//                'mapped' => false
                        'translation_domain' => 'FOSUserBundle',
                        'label' => 'form.new_password',
                        'required' => false,
                        'attr' => [
                            'class' => 'password'
                        ]
                    ));
            } else {
                $form
                    ->add('plainPassword', PasswordType::class, array(//                'mapped' => false
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
            'data_class' => 'Mondofute\Bundle\ClientBundle\Entity\ClientUser'
        ));
    }
}
