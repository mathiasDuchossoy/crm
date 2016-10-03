<?php

namespace Mondofute\Bundle\PrestationAnnexeBundle\Form;

use Mondofute\Bundle\GeographieBundle\Repository\DepartementRepository;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\FamillePrestationAnnexe;
use Mondofute\Bundle\PrestationAnnexeBundle\Entity\SousFamillePrestationAnnexe;
use Mondofute\Bundle\FournisseurPrestationAnnexeBundle\Entity\Type;
use Mondofute\Bundle\PrestationAnnexeBundle\Repository\FamillePrestationAnnexeRepository;
use Mondofute\Bundle\PrestationAnnexeBundle\Repository\SousFamillePrestationAnnexeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationAnnexeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options["locale"];
        $builder
            ->add('famillePrestationAnnexe', EntityType::class, array(
                'class' => FamillePrestationAnnexe::class,
                'required' => true,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir une famille ---",
                'query_builder' => function (FamillePrestationAnnexeRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale);
                },
                'attr' => array(
                    'onchange' => 'javascript:sortSousFamilleByFamille(this);'
                )
            ))
            ->add('sousFamillePrestationAnnexe', EntityType::class, array(
                'class' => SousFamillePrestationAnnexe::class,
                'required' => false,
                "choice_label" => "traductions[0].libelle",
                "placeholder" => " --- choisir une/des sous-famille(s) ---",
                'query_builder' => function (SousFamillePrestationAnnexeRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale);
                }
            ))
//            ->add('sousFamillePrestationAnnexes', EntityType::class, array(
//                'class' => SousFamillePrestationAnnexe::class,
//                'required' => false,
//                "choice_label" => "traductions[0].libelle",
//                "placeholder" => " --- choisir une/des sous-famille(s) ---",
//                'query_builder' => function (SousFamillePrestationAnnexeRepository $rr) use ($locale) {
//                    return $rr->getTraductionsByLocale($locale);
//                },
//                'multiple' => true
//            ))
//            ->add('type', ChoiceType::class, array(
//                'choices' => array(
//                    Type::getLibelle(Type::Individuelle) => Type::Individuelle,
//                    Type::getLibelle(Type::Quantite) => Type::Quantite,
//                    Type::getLibelle(Type::Forfait) => Type::Forfait,
//                ),
//                "placeholder" => " --- choisir un type ---",
//                'choices_as_values' => true,
//                'label' => 'type',
//                'translation_domain' => 'messages',
////                'expanded' => true,
//                'required' => true,
//            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => PrestationAnnexeTraductionType::class,
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\PrestationAnnexeBundle\Entity\PrestationAnnexe',
            'locale' => 'fr_FR',
        ));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entitySelect = 'sousFamillePrestationAnnexe';
        /** @var FormView $viewChild */
        $choices = $view->children[$entitySelect]->vars['choices'];

        $newChoices = array();
        foreach ($choices as $key => $choice) {
            $famillePrestationAnnexeId = !empty($choice->data->getFamillePrestationAnnexe()) ? $choice->data->getFamillePrestationAnnexe()->getId() : '';
            $choice->attr = array(
                'data-famille_prestation_annexe_id' => $famillePrestationAnnexeId
            );
            $newChoices[$key] = $choice;
        }
        $view->children[$entitySelect]->vars['choices'] = $newChoices;
    }

}
