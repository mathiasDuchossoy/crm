<?php

namespace Mondofute\Bundle\SaisonBundle\Form;

use Mondofute\Bundle\SaisonBundle\Entity\Contrat;
use Mondofute\Bundle\SaisonBundle\Entity\Earlybooking;
use Mondofute\Bundle\SaisonBundle\Entity\Flux;
use Mondofute\Bundle\SaisonBundle\Entity\Stock;
use Mondofute\Bundle\SaisonBundle\Entity\ValideOptions;
use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisonFournisseurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contrat', ChoiceType::class, array(
                'choices' => array(
                    Contrat::getLibelle(Contrat::aFaire) => Contrat::aFaire,
                    Contrat::getLibelle(Contrat::demande) => Contrat::demande,
                    Contrat::getLibelle(Contrat::attenteRetour) => Contrat::attenteRetour,
                    Contrat::getLibelle(Contrat::valide) => Contrat::valide,
                    Contrat::getLibelle(Contrat::resilie) => Contrat::resilie,
                    Contrat::getLibelle(Contrat::prospect) => Contrat::prospect
                ),
                'choices_as_values' => true,
                'placeholder' => ' --- vide ---',
                'required' => false
            ))
            ->add('stock', ChoiceType::class, array(
                'choices' => array(
                    Stock::getLibelle(Stock::extranetOk) => Stock::extranetOk,
                    Stock::getLibelle(Stock::extranetASaisir) => Stock::extranetASaisir,
                    Stock::getLibelle(Stock::extranetEnAttente) => Stock::extranetEnAttente,
                    Stock::getLibelle(Stock::fluxXmlEnAttente) => Stock::fluxXmlEnAttente,
                    Stock::getLibelle(Stock::fluxXmlCodeNonVerifies) => Stock::fluxXmlCodeNonVerifies,
                    Stock::getLibelle(Stock::fluxXmlOkCodesVerifies) => Stock::fluxXmlOkCodesVerifies
                ),
                'choices_as_values' => true,
                'placeholder' => ' --- vide ---',
                'required' => false
            ))
            ->add('flux', ChoiceType::class, array(
                'choices' => array(
                    Flux::getLibelle(Flux::fluxXml) => Flux::fluxXml,
                    Flux::getLibelle(Flux::extranet) => Flux::extranet,
                    Flux::getLibelle(Flux::fluxXmlArkiane) => Flux::fluxXmlArkiane,
                    Flux::getLibelle(Flux::fluxXmlHomeResa) => Flux::fluxXmlHomeResa,
                    Flux::getLibelle(Flux::fluxXmlResalys) => Flux::fluxXmlResalys,
                ),
                'choices_as_values' => true,
                'placeholder' => ' --- vide ---',
                'required' => false
            ))
            ->add('valideOptions', ChoiceType::class, array(
                'choices' => array(
                    ValideOptions::getLibelle(ValideOptions::enLigne) => ValideOptions::enLigne,
                    ValideOptions::getLibelle(ValideOptions::aSaisir) => ValideOptions::aSaisir,
                    ValideOptions::getLibelle(ValideOptions::pasDOptions) => ValideOptions::pasDOptions,
                ),
                'choices_as_values' => true,
                'placeholder' => ' --- vide ---',
                'required' => false
            ))
            ->add('earlybooking', ChoiceType::class, array(
                'choices' => array(
                    Earlybooking::getLibelle(Earlybooking::pasDEb) => Earlybooking::pasDEb,
                    Earlybooking::getLibelle(Earlybooking::ADemander) => Earlybooking::ADemander,
                    Earlybooking::getLibelle(Earlybooking::AParametrer) => Earlybooking::AParametrer,
                    Earlybooking::getLibelle(Earlybooking::enFluxXml) => Earlybooking::enFluxXml,
                ),
                'choices_as_values' => true,
                'placeholder' => ' --- vide ---',
                'required' => false
            ))
            ->add('conditionEarlybooking', null, [
                'required' => false])
            ->add('agentMaJProd', EntityType::class, ['class' => Utilisateur::class, 'placeholder' => '----', 'required' => false])
            ->add('agentMaJSaisie', EntityType::class, ['class' => Utilisateur::class, 'placeholder' => '----', 'required' => false])//            ->add('saison')
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\SaisonBundle\Entity\SaisonFournisseur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_saisonbundle_saisonfournisseur';
    }


}
