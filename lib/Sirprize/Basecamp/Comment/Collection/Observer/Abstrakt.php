<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Comment\Collection\Observer;

use Sirprize\Basecamp\Comment\Collection;

/**
 * Abstract class to observe and print state changes of the observed comment
 */
abstract class Abstrakt
{

    abstract public function onStartSuccess(Collection $collection);
    abstract public function onStartError(Collection $collection);

    protected function _getOnStartSuccessMessage(Collection $collection)
    {
        return "started comment collection. found ".$collection->count()." comments";
    }

    protected function _getOnStartErrorMessage(Collection $collection)
    {
        return "comment collection could not be started";
    }

}