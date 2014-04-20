<?php

namespace Eventual\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class Eventual extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
