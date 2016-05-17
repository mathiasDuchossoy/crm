<?php

namespace Mondofute\Bundle\ClientBundle\Form;

use Nucleus\ContactBundle\Entity\Civilite;
use ReflectionClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $today = new \DateTime();
        $builder
            ->add('civilite', EntityType::class, array(
                'class' => Civilite::class,
                'property' => 'libelle'
            ))
            ->add('prenom')
            ->add('nom')
            ->add('vip')
            ->add('dateNaissance', 'date', array(
                'years' => range(1900, date_format($today, 'Y')),
                'choice_translation_domain' => null
            ))// todo: mettre le datepicker
            ->add('moyenComs', 'infinite_form_polycollection', array(
                'types' => array(
                    'nucleus_moyencombundle_adresse',
                    'nucleus_moyencombundle_telfixe',
                    'nucleus_moyencombundle_telmobile',
                    'nucleus_moyencombundle_email',
                ),
//                'by'
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\ClientBundle\Entity\Client'
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $cViewComm = [];
        foreach ($view->children['moyenComs']->children as $viewMoyenComs) {
            $typeComm = (new ReflectionClass($viewMoyenComs->vars['value']))->getShortName();
//            dump($viewMoyenComs);
            $viewMoyenComs->vars['type'] = $typeComm;
            $viewMoyenComs->vars['label'] = $typeComm;
            if (empty($cViewComm[$typeComm])) {
                $cViewComm[$typeComm] = [];
            }
            array_push($cViewComm[$typeComm], $viewMoyenComs);
        }
        foreach ($cViewComm as $viewCom) {
            foreach ($viewCom as $key => $com) {
//                if ($key > 0) {
                $com->vars['label'] = $com->vars['label'] . ' ' . ($key + 1);
//                }
            }
        }
    }

}
