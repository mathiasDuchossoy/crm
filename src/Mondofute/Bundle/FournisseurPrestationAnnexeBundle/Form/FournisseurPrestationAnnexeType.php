<?php

namespace Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Form;

use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\Type;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurPrestationAnnexeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    Type::getLibelle(Type::Individuelle) => Type::Individuelle,
                    Type::getLibelle(Type::Quantite) => Type::Quantite,
                    Type::getLibelle(Type::Forfait) => Type::Forfait,
                ),
                "placeholder" => " --- choisir un type ---",
                'choices_as_values' => true,
                'label' => 'type',
                'translation_domain' => 'messages',
//                'expanded' => true,
                'required' => true,
            ))
            ->add('capacite', FournisseurPrestationAnnexeCapaciteType::class, array('required' => false,))
            ->add('dureeSejour', FournisseurPrestationAnnexeDureeSejourType::class, array('required' => false,))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => FournisseurPrestationAnnexeTraductionType::class,
            ))
//            ->add('prestationAnnexe', EntityType::class, array(
//                'class' => PrestationAnnexe::class,
//                'required' => true,
//                "choice_label" => "id",
//            ))
            ->add('prestationAnnexe', EntityType::class, array(
                'class' => PrestationAnnexe::class,
                'choice_label' => 'id',
                'label_attr' => [
                    'style' => 'display:none',
                ],
                'attr' => [
                    'style' => 'display:none',
                ],
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\FournisseurPrestationAnnexe',
            'famillePrestationAnnexeId' => null,
        ));
    }
}
