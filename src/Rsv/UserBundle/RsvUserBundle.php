<?php

namespace Rsv\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RsvUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
