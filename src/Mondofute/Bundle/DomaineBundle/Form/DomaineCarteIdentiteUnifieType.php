<?php

namespace Mondofute\Bundle\DomaineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineCarteIdentiteUnifieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domaineCarteIdentites', CollectionType::class,
                array('entry_type' => DomaineCarteIdentiteType::class));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\DomaineBundle\Entity\DomaineCarteIdentiteUnifie',
            'locale' => 'fr_FR'
        ));
    }


    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        foreach ($view->children['domaineCarteIdentites'] as $domaineCarteIdentite) {
            if ($domaineCarteIdentite->vars['value']->getSite()->getCrm() == 1) {
                $domaineCarteIdentiteCrm = $domaineCarteIdentite;
            } else {
                foreach ($domaineCarteIdentite->children['images'] as $key => $image) {
                    if ($image->vars['value']->getActif() == true) {
                        $siteId = $domaineCarteIdentite->vars['value']->getSite()->getId();
                        $domaineCarteIdentiteCrm->children['images']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
                foreach ($domaineCarteIdentite->children['photos'] as $key => $photo) {
                    if ($photo->vars['value']->getActif() == true) {
                        $siteId = $domaineCarteIdentite->vars['value']->getSite()->getId();
                        $domaineCarteIdentiteCrm->children['photos']->children[$key]->children['sites']->children[$siteId]->vars['attr'] = array('checked' => 'checked');
                    }
                }
            }
        }
    }

}
