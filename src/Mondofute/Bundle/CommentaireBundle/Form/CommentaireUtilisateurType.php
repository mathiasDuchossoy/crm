<?php

namespace Mondofute\Bundle\CommentaireBundle\Form;

use Mondofute\Bundle\UtilisateurBundle\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireUtilisateurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        global $kernel;
//        dump($options['user']);die;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $data = $builder->getData();

//        dump($data);
        if ($data && null !== $data->getId()) {
            $builder
                ->add('utilisateur', EntityType::class, [
                    'class' => Utilisateur::class
                ]);
        } else {
            $builder
                ->add('utilisateur', EntityType::class, [
                    'class' => Utilisateur::class,
//                    'data' => $options['user'],
//                    'empty_data' => $options['user'],
//                    'empty_value'=> $options['user'],
                ]);
        }

        $builder
            ->add('contenu');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommentaireBundle\Entity\CommentaireUtilisateur',
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commentairebundle_commentaireutilisateur';
    }


}
