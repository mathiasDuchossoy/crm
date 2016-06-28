<?php

namespace Mondofute\Bundle\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MondofuteMediaBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ApplicationSonataMediaBundle';
    }
}
