<?php

namespace Mondofute\Bundle\HebergementBundle\Form;

use Mondofute\Bundle\HebergementBundle\Entity\UniteClassementHebergement;
use Mondofute\Bundle\HebergementBundle\Repository\UniteClassementHebergementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassementHebergementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('valeur')
//            ->add('unite', EntityType::class, array('class' => UniteClassementHebergement::class, 'choice_label' => 'id'))
            ->add('unite', EntityType::class, array(
                'class' => UniteClassementHebergement::class,
                'placeholder' => '--- Veuillez choisir une unité de classement ---',
                'choice_label' => 'traductions[0].libelle',
                'query_builder' => function (UniteClassementHebergementRepository $uch) use ($locale) {
                    return $uch->getTraductionsByLocale($locale);
                },
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\HebergementBundle\Entity\ClassementHebergement',
            'locale' => 'fr_FR'
        ));
    }
}
