<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Company;

use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Response;
use Sirprize\Basecamp\Exception;
use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Company\Entity;
use Sirprize\Basecamp\Company\Collection\Observer\Abstrakt;

/**
 * Encapsulate a set of persisted company objects and the operations performed over them
 */
class Collection extends \SplObjectStorage
{

    const _COMPANY = 'company';

    protected $_service = null;
    protected $_httpClient = null;
    protected $_started = false;
    protected $_loaded = false;
    protected $_response = null;
    protected $_observers = array();

    public function setService(Service $service)
    {
        $this->_service = $service;
        return $this;
    }

    public function setHttpClient(\Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    /**
     * Get response object
     *
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Attach observer object
     *
     * @return \Sirprize\Basecamp\Company\Collection
     */
    public function attachObserver(Abstrakt $observer)
    {
        $exists = false;

        foreach(array_keys($this->_observers) as $key)
        {
            if($observer === $this->_observers[$key])
            {
                $exists = true;
                break;
            }
        }

        if(!$exists)
        {
            $this->_observers[] = $observer;
        }

        return $this;
    }

    /**
     * Detach observer object
     *
     * @return \Sirprize\Basecamp\Company\Collection
     */
    public function detachObserver(Abstrakt $observer)
    {
        foreach(array_keys($this->_observers) as $key)
        {
            if($observer === $this->_observers[$key])
            {
                unset($this->_observers[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Instantiate a new Company entity
     *
     * @return \Sirprize\Basecamp\Company\Entity
     */
    public function getCompanyInstance()
    {
        $company = new Entity();
        $company
            ->setHttpClient($this->_getHttpClient())
            ->setService($this->_getService())
        ;

        return $company;
    }

    /**
     * Defined by \SplObjectStorage
     *
     * Add company entity
     *
     * @param \Sirprize\Basecamp\Company\Entity $company
     * @throws Exception
     * @return \Sirprize\Basecamp\Company\Collection
     */
    public function attach($company, $data = null)
    {
        if(!$company instanceof Entity)
        {
            throw new Exception('expecting an instance of \Sirprize\Basecamp\Company\Entity');
        }

        parent::attach($company);
        return $this;
    }

    /**
     * Fetch company by id
     *
     * @throws Exception
     * @return null|\Sirprize\Basecamp\Company\Entity
     */
    public function startById(Id $id)
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/companies/$id.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->setHeaders('Content-Type', 'application/xml')
                ->setHeaders('Accept', 'application/xml')
                ->request('GET')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('GET');
            }
            catch(\Exception $exception)
            {
                $this->_onStartError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onStartError();
            return null;
        }

        $this->load($this->_response->getData());
        $this->_onStartSuccess();
        $this->rewind();
        return $this->current();
    }

    /**
     * Fetch all companies
     *
     * @throws Exception
     * @return \Sirprize\Basecamp\Company\Collection
     */
    public function startAll()
    {
        if($this->_started)
        {
            return $this;
        }

        $this->_started = true;

        try {
            $response = $this->_getHttpClient()
                ->setUri($this->_getService()->getBaseUri()."/companies.xml")
                ->setAuth($this->_getService()->getUsername(), $this->_getService()->getPassword())
                ->request('GET')
            ;
        }
        catch(\Exception $exception)
        {
            try {
                // connection error - try again
                $response = $this->_getHttpClient()->request('GET');
            }
            catch(\Exception $exception)
            {
                $this->_onStartError();

                throw new Exception($exception->getMessage());
            }
        }

        $this->_response = new Response($response);

        if($this->_response->isError())
        {
            // service error
            $this->_onStartError();
            return $this;
        }

        $this->load($this->_response->getData());
        $this->_onStartSuccess();
        return $this;
    }

    /**
     * Instantiate company objects with api response data
     *
     * @return \Sirprize\Basecamp\Company\Collection
     */
    public function load(\SimpleXMLElement $xml)
    {
        if($this->_loaded)
        {
            throw new Exception('collection has already been loaded');
        }

        $this->_loaded = true;

        if(isset($xml->id))
        {
            // request for a single entity
            $company = $this->getCompanyInstance();
            $company->load($xml);
            $this->attach($company);
            return $this;
        }

        $array = (array) $xml;

        if(!isset($array[self::_COMPANY]))
        {
            // list request - 0 items in response
            return $this;
        }

        if(isset($array[self::_COMPANY]->id))
        {
            // list request - 1 item in response
            $company = $this->getCompanyInstance();
            $company->load($array[self::_COMPANY]);
            $this->attach($company);
            return $this;
        }

        foreach($array[self::_COMPANY] as $row)
        {
            // list request - 2 or more items in response
            $company = $this->getCompanyInstance();
            $company->load($row);
            $this->attach($company);
        }

        return $this;
    }

    protected function _getService()
    {
        if($this->_service === null)
        {
            throw new Exception('call setService() before '.__METHOD__);
        }

        return $this->_service;
    }

    protected function _getHttpClient()
    {
        if($this->_httpClient === null)
        {
            throw new Exception('call setHttpClient() before '.__METHOD__);
        }

        return $this->_httpClient;
    }

    protected function _onStartSuccess()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onStartSuccess($this);
        }
    }

    protected function _onStartError()
    {
        foreach($this->_observers as $observer)
        {
            $observer->onStartError($this);
        }
    }

}