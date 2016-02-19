<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Message\Collection\Observer;

use Sirprize\Basecamp\Message\Collection;
use Sirprize\Basecamp\Message\Collection\Observer\Abstrakt;

/**
 * Class to observe and print state changes of the observed message
 */
class Stout extends Abstrakt
{

    public function onStartSuccess(Collection $collection)
    {
        print $this->_getOnStartSuccessMessage($collection)."\n";
    }

    public function onStartError(Collection $collection)
    {
        print $this->_getOnStartErrorMessage($collection)."\n";
    }

}