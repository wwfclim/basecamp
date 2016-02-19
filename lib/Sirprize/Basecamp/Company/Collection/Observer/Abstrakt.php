<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Company\Collection\Observer;

use Sirprize\Basecamp\Company\Collection;

/**
 * Abstract class to observe and print state changes of the observed company
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started company collection. found ".$collection->count()." companies";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "company collection could not be started";
    }

}