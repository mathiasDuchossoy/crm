<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enseigne')
            ->add('interlocuteurs', CollectionType::class, array(
                    'entry_type' => FournisseurInterlocuteurType::class,
                    'allow_add' => true,
//                    'entry_options' => array(
//                        'locale' => $options['locale'] ,
//                        'siteDomaineParent' => $siteDomaineParent,
//                        'domaineUnifieId' => $domaineUnifieId
//                    )
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur'
        ));
    }
}
