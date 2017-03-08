<?php

namespace Mondofute\Bundle\ClientBundle\Form;

use Mondofute\Bundle\ClientBundle\Entity\ClientUser;
use Nucleus\ContactBundle\Entity\Civilite;
use ReflectionClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientClientUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('id')
            ->add('civilite', EntityType::class, array(
                'class' => Civilite::class,
                'property' => 'libelle'
            ))
            ->add('prenom')
            ->add('nom')
            ->add('vip')
            ->add('dateNaissance', 'birthday', array(
//                'locale' => 'fr_FR'
//                'months' => 'fr'
//                'years' => range(1900, date_format($today, 'Y')),
                'model_timezone' => 'Europe/Paris',
                'view_timezone' => 'Europe/Paris',
                'format' => 'yyyy-MM-dd'
            ))
            ->add('moyenComs', 'infinite_form_polycollection', array(
                'types' => array(
                    'nucleus_moyencombundle_adresse',
                    'nucleus_moyencombundle_telfixe',
                    'nucleus_moyencombundle_telmobile',
                    'nucleus_moyencombundle_email',
                ),
            ))
            ->add('clientUser', ClientUserAloneType::class, array(
                'data_class' => ClientUser::class,
//                'label_attr' => array('style'=>'display:none')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\ClientBundle\Entity\Client'
        ));
    }

    public function getBlockPrefix()
    {
        return 'mondofute_client_bundle_client_client_user';
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $cViewComm = [];
        foreach ($view->children['moyenComs']->children as $viewMoyenComs) {
            $typeComm = (new ReflectionClass($viewMoyenComs->vars['value']))->getShortName();

            if (empty($login) && $typeComm == "Email") {
                $login = true;
                $viewMoyenComs->children['adresse']->vars['required'] = true;
            }
            $viewMoyenComs->vars['type'] = $typeComm;
            $viewMoyenComs->vars['label'] = $typeComm;
            if (empty($cViewComm[$typeComm])) {
                $cViewComm[$typeComm] = [];
            }
            array_push($cViewComm[$typeComm], $viewMoyenComs);
        }
        foreach ($cViewComm as $viewCom) {
            foreach ($viewCom as $key => $com) {
                $com->vars['label'] = $com->vars['label'] . ' ' . ($key + 1);
            }
        }
    }
}
