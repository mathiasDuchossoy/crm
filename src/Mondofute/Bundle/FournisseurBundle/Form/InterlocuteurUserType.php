<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterlocuteurUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

//            dump($event);
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
//        $builder
//            ->add('plainPassword', TextType::class, array(//                'mapped' => false
//                'translation_domain' => 'FOSUserBundle',
//                'label' => 'form.password',
//            ))
//            ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
//                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
//                'options' => array('translation_domain' => 'FOSUserBundle'),
//                'first_options' => array('label' => 'form.password'),
//                'second_options' => array('label' => 'form.password_confirmation'),
//                'invalid_message' => 'fos_user.password.mismatch',
//                'required' => false,
//            ))
//        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurUser'
        ));
    }
}
