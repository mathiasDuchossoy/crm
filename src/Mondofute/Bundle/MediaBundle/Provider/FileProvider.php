<?php
namespace Mondofute\Bundle\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\FileProvider as BaseFileProvider;
use Symfony\Component\Form\FormBuilder;

class FileProvider extends BaseFileProvider
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