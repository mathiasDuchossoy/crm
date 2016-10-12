<?php

namespace Mondofute\Bundle\CodePromoApplicationBundle\Form;

use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CodePromoFamillePrestationAnnexeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('famillePrestationAnnexe' , EntityType::class , array(
                'class' => FamillePrestationAnnexe::class,
                'property' => 'id'
            ))
            ->add('fournisseur')
//            ->add('codePromo')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CodePromoApplicationBundle\Entity\CodePromoFamillePrestationAnnexe'
        ));
    }
}
