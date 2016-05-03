<?php

namespace Mondofute\Bundle\UtilisateurBundle\Form;

use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('login')
            ->add('password')
            ->add('moyenComs', 'infinite_form_polycollection', array(
                'types' => array(
                    'nucleus_moyencombundle_email',
                )
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur'
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
                if ($key > 0) {
                    $com->vars['label'] = $com->vars['label'] . ' ' . $key;
                }
            }
        }
    }

}
