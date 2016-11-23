<?php

namespace Mondofute\Bundle\LogementBundle\Form;

use Mondofute\Bundle\HebergementBundle\Entity\FournisseurHebergement;
use Mondofute\Bundle\LogementBundle\Entity\NombreDeChambre;
use Mondofute\Bundle\LogementBundle\Repository\NombreDeChambreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['locale'];
        $builder
            ->add('accesPMR', null, array('label' => 'Acces.PMR', 'translation_domain' => 'messages'))
            ->add('capacite')
            ->add('superficieMin')
            ->add('superficieMax')
            ->add('traductions', CollectionType::class, array(
                'entry_type' => LogementTraductionType::class,
            ))
            ->add('site', HiddenType::class, array('mapped' => false))
            ->add('fournisseurHebergement', EntityType::class,
                array('class' => FournisseurHebergement::class, 'choice_label' => 'id'))
            ->add('photos', CollectionType::class, array(
                'entry_type' => LogementPhotoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('nombreDeChambre', EntityType::class, array(
                'class' => NombreDeChambre::class,
                'required' => true,
                "choice_label" => "traductions[0].libelle",
                'query_builder' => function (NombreDeChambreRepository $r) use ($locale) {
                    return $r->getTraductionsByLocale($locale);
                },
                'expanded' => true,
            ))
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

}
