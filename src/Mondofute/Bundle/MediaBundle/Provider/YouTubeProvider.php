<?php
namespace Mondofute\Bundle\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\YouTubeProvider as BaseYouTubeProvider;
use Symfony\Component\Form\FormBuilder;

class YouTubeProvider extends BaseYouTubeProvider
{
    /**
     * {@inheritdoc}
     */
    public function buildMediaType(FormBuilder $formBuilder)
    {
        $formBuilder->add('binaryContent', 'text', array(
            'label' => false,
        ));
    }

}