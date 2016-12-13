<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PisteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['locale'];
        $builder
            ->add('nombre')
            ->add('typePiste', HiddenType::class, array('mapped' => false))
//            ->add('typePiste', EntityType::class, array(
//                'class' => TypePiste::class,
//                'required' => false,
//                'choice_label' => 'traductions[0].libelle',
//                'query_builder' => function (TypePisteRepository $rr) use ($locale) {
//                    return $rr->getTraductionsByLocale($locale);
//                },
//            ))
//            ->add('domaineCarteIdentite')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\Piste',
            'locale' => 'fr_FR',
        ));
    }
}
