<?php

namespace Mondofute\Bundle\DescriptionForfaitSkiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleDescriptionForfaitSkiType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('descriptionForfaitSkis', CollectionType::class,
            array('entry_type' => DescriptionForfaitSkiType::class, 'entry_options' => array('locale' => $options["locale"])));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DescriptionForfaitSkiBundle\Entity\ModeleDescriptionForfaitSki',
            'locale' => 'fr_FR'
        ));
    }
}
