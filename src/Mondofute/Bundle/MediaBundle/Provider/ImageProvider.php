<?php
namespace Mondofute\Bundle\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;
use Symfony\Component\Form\FormBuilder;

class ImageProvider extends BaseImageProvider
{
    /**
     * {@inheritdoc}
     */
    public function buildMediaType(FormBuilder $formBuilder)
    {
        $formBuilder->add('binaryContent', 'file', array(
            'label' => false,
        ));
    }
}