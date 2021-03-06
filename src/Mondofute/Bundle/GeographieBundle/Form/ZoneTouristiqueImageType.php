<?php

namespace Mondofute\Bundle\GeographieBundle\Form;

use Mondofute\Bundle\SiteBundle\Repository\SiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneTouristiqueImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $doctrine = $kernel->getContainer()->get('doctrine');

        $siteRepository = $doctrine->getRepository('MondofuteSiteBundle:Site');
        $sites = $siteRepository->chargerSansCrmParClassementAffichage();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($sites) {
            $data = $event->getData();
            $form = $event->getForm();

            // vérifie si l'objet Image est "nouveau"
            // Si aucune donnée n'est passée au formulaire, la donnée est "null".
            // Ce doit être considéré comme un nouveau "Image"
            if ($data && null !== $data->getId()) {
                $form
                    ->add('image', 'sonata_media_type', array(
                        'provider' => 'sonata.media.provider.image',
                        'context' => 'zone_touristique_image_crm',
                        'required' => false,
                        'label' => 'image',
                    ))
                    ->add('sites', EntityType::class, array(
                        'class' => 'MondofuteSiteBundle:Site',
                        'choice_label' => 'libelle',
                        'multiple' => true,
                        'expanded' => true,
                        'query_builder' => function (SiteRepository $rr) {
                            return $rr->getSitesSansCrm();
                        },
                        'mapped' => false,
                        'attr' => ['class' => 'form-inline'],
//                        'data' => $sites,
                    ));
            } else {
                $form
                    ->add('image', 'sonata_media_type', array(
                        'provider' => 'sonata.media.provider.image',
                        'context' => 'zone_touristique_image_crm',
                        'required' => true,
                        'label' => 'image',
                    ))
                    ->add('sites', EntityType::class, array(
                        'class' => 'MondofuteSiteBundle:Site',
                        'choice_label' => 'libelle',
                        'multiple' => true,
                        'expanded' => true,
                        'query_builder' => function (SiteRepository $rr) {
                            return $rr->getSitesSansCrm();
                        },
                        'mapped' => false,
                        'attr' => ['class' => 'form-inline'],
                        'data' => $sites,
                    ));
            }
        });

        $builder
            ->add('_type', 'hidden', array(
                'data' => $this->getName(),
                'mapped' => false
            ))
            ->add('traductions', CollectionType::class, array(
                'entry_type' => ZoneTouristiqueImageTraductionType::class,
                'allow_add' => true,
                'prototype_name' => '__name_traduction__',
                'required' => true,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\GeographieBundle\Entity\ZoneTouristiqueImage',
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $view->children['image']->children['binaryContent']->vars['attr'] = array("accept" => "image/x-png, image/gif, image/jpeg");
    }


}
