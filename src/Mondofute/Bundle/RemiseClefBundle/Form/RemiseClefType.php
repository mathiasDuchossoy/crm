<?php

namespace Mondofute\Bundle\RemiseClefBundle\Form;

use Mondofute\Bundle\FournisseurBundle\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseClefType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsHoraires = array(
            'widget' => 'choice',
            'hours' => array(8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20),
            'minutes' => array(0, 30)
        );
        $builder
            ->add('fournisseur', EntityType::class, array(
                'class' => Fournisseur::class,
                'choice_label' => 'id',
                'attr' => array('style' => 'display:none'),
                'label_attr' => array('style' => 'display: none')
            ))
            ->add('libelle')
            ->add('heureRemiseClefLongSejour', TimeType::class, $optionsHoraires)
            ->add('heureRemiseClefCourtSejour', TimeType::class, $optionsHoraires)
            ->add('heureDepartLongSejour', TimeType::class, $optionsHoraires)
            ->add('heureDepartCourtSejour', TimeType::class, $optionsHoraires)
            ->add('heureTardiveLongSejour', TimeType::class, $optionsHoraires)
            ->add('heureTardiveCourtSejour', TimeType::class, $optionsHoraires)
            ->add('standard')
            ->add('traductions', CollectionType::class, array('entry_type' => RemiseClefTraductionType::class));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\RemiseClefBundle\Entity\RemiseClef'
        ));
    }
}
