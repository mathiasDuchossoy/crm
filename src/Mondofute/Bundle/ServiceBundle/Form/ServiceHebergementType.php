<?php

namespace Mondofute\Bundle\ServiceBundle\Form;

use Mondofute\Bundle\ServiceBundle\Entity\ServiceHebergement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceHebergementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder->addEventListener()
        $builder
            ->add('service')
            ->add('tarifs', CollectionType::class,
                array('entry_type' => ServiceHebergementTarifType::class, 'allow_add' => true, 'allow_delete' => true));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

//            dump($event);
            // vérifie si l'objet Product est "nouveau"
            // Si aucune donnée n'est passée au formulaire, la donnée est "null".
            // Ce doit être considéré comme un nouveau "Product"
            if ($user && null !== $user->getId()) {
                $form
                    ->add('checkbox', CheckboxType::class,
                        array('mapped' => false, 'label' => false, 'data' => true, 'required' => false));
            } else {
                $form
                    ->add('checkbox', CheckboxType::class,
                        array('mapped' => false, 'label' => false, 'required' => false));

            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ServiceHebergement::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'mondofute_service_bundle_service_hebergement_type';
    }

    public function getName()
    {
        return 'mondofute_service_bundle_service_hebergement_type';
    }
}
