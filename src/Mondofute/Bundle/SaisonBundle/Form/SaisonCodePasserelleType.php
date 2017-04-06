<?php

namespace Mondofute\Bundle\SaisonBundle\Form;

use Mondofute\Bundle\PasserelleBundle\Form\CodePasserelleType;
use Mondofute\Bundle\SaisonBundle\Entity\Saison;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisonCodePasserelleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('saison', EntityType::class, [
                    'class' => Saison::class,
                    'choice_label' => 'id',
                    'label_attr' => [
                        'style' => 'display:none',
                    ],
                    'attr' => [
                        'style' => 'display:none',
                    ]
                ]
            )
            ->add('button', ButtonType::class,
                array(
                    'label' => 'ajouter',
                    'attr' => array('class' => 'btn btn-default addCodePasserelle', 'title' => 'ajouter.codePasserelle')
                ))
            ->add('codePasserelles', CollectionType::class,
                [
                    'prototype_name' => '__name_code_passerelle_label__',
                    'entry_type' => CodePasserelleType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\SaisonBundle\Entity\SaisonCodePasserelle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_saisonbundle_saisoncodepasserelle';
    }


}
