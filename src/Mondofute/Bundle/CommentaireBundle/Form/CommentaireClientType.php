<?php

namespace Mondofute\Bundle\CommentaireBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireClientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('contenu')
            ->add('reponses', CollectionType::class, array(
                'entry_type' => CommentaireUtilisateurType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => [
                    'user' => $options['user']
                ]
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommentaireBundle\Entity\CommentaireClient',
            'user' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commentairebundle_commentaireclient';
    }


}
