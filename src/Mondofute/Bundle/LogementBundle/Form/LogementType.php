<?php

namespace Mondofute\Bundle\LogementBundle\Form;

use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('actif')
            ->add('accesPMR', null, array('label' => 'Acces.PMR', 'translation_domain' => 'messages'))
            ->add('capacite')
            ->add('nbChambre', null, array('label' => 'Nb.Chambre', 'translation_domain' => 'messages'))
            ->add('superficieMin')
            ->add('superficieMax')
            ->add('traductions', CollectionType::class, array(
                'entry_type' => LogementTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('fournisseurHebergement', EntityType::class,
                array('class' => FournisseurHebergement::class, 'choice_label' => 'id'))
//            ->add('logementUnifie')
//            ->add('hebergement', EntityType::class,
//                array('class' => FournisseurHebergement::class, 'choice_label' => 'id'))
//            ->add('hebergements', EntityType::class,
//                array('class' => Hebergement::class, 'choice_label' => 'id', 'mapped' => false))
//            ->add('fournisseur', EntityType::class,
//                array('class' => Fournisseur::class, 'choice_label' => 'id', 'mapped' => false))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\LogementBundle\Entity\Logement',
            'locale' => 'fr_FR'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
//        foreach ($view->children['hebergement']->vars['choices'] as $choice) {
//            $choice->attr['data-fournisseur'] = $choice->data->getFournisseur()->getId();
//            $choice->attr['data-hebergement'] = $choice->data->getHebergement()->getId();
//        }
//        dump($view->children['hebergement']->vars['choices']); die;
    }
}
