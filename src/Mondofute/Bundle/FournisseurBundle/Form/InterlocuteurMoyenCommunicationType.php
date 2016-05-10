<?php

namespace Mondofute\Bundle\FournisseurBundle\Form;

use commun\moyencommunicationBundle\Form\MobileType;
use Mondofute\Bundle\FournisseurBundle\Entity\InterlocuteurFonction;
use Mondofute\Bundle\FournisseurBundle\Entity\ServiceInterlocuteur;
use Mondofute\Bundle\FournisseurBundle\Repository\InterlocuteurFonctionRepository;
use Mondofute\Bundle\FournisseurBundle\Repository\ServiceInterlocuteurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterlocuteurMoyenCommunicationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($builder);die;
        $builder
            ->add('mobile', MobileType::class)
//            ->add('fixe', 'commun\moyencommunicationBundle\Form\FixeType' , array('label' => true , 'label_attr' => array('style' => 'display:none')))
//            ->add('telephone1', 'commun\moyencommunicationBundle\Form\FixeType' , array('label' => 'téléphone 1'))
//            ->add('telephone2', 'commun\moyencommunicationBundle\Form\FixeType' , array('label' => 'téléphone 2'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'commun\moyencommunicationBundle\Entity\MoyenCommunication',
            'locale' => 'fr_FR'
        ));
    }
}
