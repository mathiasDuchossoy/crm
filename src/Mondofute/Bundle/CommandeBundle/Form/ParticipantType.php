<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use Nucleus\ContactBundle\Entity\Civilite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite', EntityType::class, [
                'class' => Civilite::class,
                'choice_label' => 'libelle',
                'empty_value' => ''
            ])
            ->add('prenom')
            ->add('nom')
            ->add('dateNaissance', BirthdayType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\Participant'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_participant';
    }


}
