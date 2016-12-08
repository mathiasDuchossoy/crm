<?php

namespace Mondofute\Bundle\ClientBundle\Form;

use Mondofute\Bundle\ClientBundle\Entity\Client;
use Symfony\Component\Form\AbstractType;
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
                    ->add('plainPassword', TextType::class, array(//                'mapped' => false
                        'translation_domain' => 'FOSUserBundle',
                        'label' => 'form.new_password',
                        'required' => false
                    ));
            } else {
                $form
                    ->add('plainPassword', TextType::class, array(//                'mapped' => false
                        'translation_domain' => 'FOSUserBundle',
                        'label' => 'form.password',
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
