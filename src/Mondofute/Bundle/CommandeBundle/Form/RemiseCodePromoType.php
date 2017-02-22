<?php

namespace Mondofute\Bundle\CommandeBundle\Form;

use AppCache;
use Mondofute\Bundle\CodePromoBundle\Entity\CodePromo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseCodePromoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $site = $options['site'];
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            /** @var AppCache $kernel */
            $kernel = $kernel->getKernel();
        }
        /** @var Kernel $kernel */
        $doctrine = $kernel->getContainer()->get('doctrine');

        $codePromoRepository = $doctrine->getRepository('MondofuteCodePromoBundle:CodePromo');
        $data = $builder->getData();

        if ($data && null !== $data->getId()) {
            $codePromos = $codePromoRepository->findAll();
        } else {
            $codePromos = $codePromoRepository->findBy(['site' => $site]);
        }

        $builder
            ->add('codePromo', EntityType::class, [
                'class' => CodePromo::class,
                'choice_label' => 'libelle',
                'attr' => [
                    'class' => 'js-code-promo',
                    'data-site' => $site->getId()
                ],
                'empty_value' => ' --- Rechercher un code promo ---'
            ])
            ->add('_type', HiddenType::class, array(
                'data' => 'remiseCodePromo', // Arbitrary, but must be distinct
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mondofute\Bundle\CommandeBundle\Entity\RemiseCodePromo',
            'model_class' => 'Mondofute\Bundle\CommandeBundle\Entity\RemiseCodePromo',
            'site' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mondofute_bundle_commandebundle_remisecodepromo';
    }


}
