<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Company;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;

/**
 * Represent and modify a company
 */
class Entity
{
	const _ID = 'id';
	const _NAME = 'name';
	const _ADDRESS_ONE = 'address-one';
	const _ADDRESS_TWO = 'address-two';
	const _CITY = 'city';
	const _STATE = 'state';
	const _ZIP = 'zip';
	const _COUNTRY = 'country';
	const _WEB_ADDRESS = 'web-address';
	const _PHONE_NUMBER_OFFICE = 'phone-number-office';
	const _PHONE_NUMBER_FAX = 'phone-number-fax';
	const _TIME_ZONE_ID = 'time-zone-id';
	const _CAN_SEE_PRIVATE = 'can_see_private';
	const _URL_NAME = 'url-name';
    #const _TOKEN = 'token';

    protected $_service = null;
    protected $_httpClient = null;
    protected $_data = array();
    protected $_loaded = false;
    protected $_response = null;

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
     * @return \Sirprize\Basecamp\Response|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Sirprize\Basecamp\Id
     */
    public function getId()
    {
        return $this->_getVal(self::_ID);
    }

    public function getName()
    {
        return $this->_getVal(self::_NAME);
    }
    
    public function getAddressOne()
    {
        return $this->_getVal(self::_ADDRESS_ONE);
    }
    
    public function getAddressTwo()
    {
        return $this->_getVal(self::_ADDRESS_TWO);
    }
    
    public function getCity()
    {
        return $this->_getVal(self::_CITY);
    }
    
    public function getState()
    {
        return $this->_getVal(self::_STATE);
    }
    
    public function getZip()
    {
        return $this->_getVal(self::_ZIP);
    }
    
    public function getCountry()
    {
        return $this->_getVal(self::_COUNTRY);
    }
    
    public function getWebAddress()
    {
        return $this->_getVal(self::_WEB_ADDRESS);
    }
    
    public function getPhoneNumberOffice()
    {
        return $this->_getVal(self::_PHONE_NUMBER_OFFICE);
    }
    
    public function getPhoneNumberFax()
    {
        return $this->_getVal(self::_PHONE_NUMBER_FAX);
    }
    
    public function getTimeZoneId()
    {
        return $this->_getVal(self::_TIME_ZONE_ID);
    }
    
    public function getCanSeePrivate()
    {
        return $this->_getVal(self::_CAN_SEE_PRIVATE);
    }
    
    public function getUrlName()
    {
        return $this->_getVal(self::_URL_NAME);
    }

    /*
    public function getToken()
    {
        return $this->_getVal(self::_TOKEN);
    }
    */

    /**
     * Load data returned from an api request
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Company
     */
    public function load(\SimpleXMLElement $xml, $force = false)
    {
        if($this->_loaded && !$force)
        {
            throw new Exception('entity has already been loaded');
        }

        #print_r($xml); exit;
        $this->_loaded = true;
        $array = (array) $xml;

        $id = new Id($array[self::_ID]);

        if (isset($array[self::_CAN_SEE_PRIVATE]))
        {
          $canSeePrivate = ($array[self::_CAN_SEE_PRIVATE] == 'true');
        }
        else
        {
          $canSeePrivate = false;
        }


        $this->_data = array(
	        self::_ID => $id,
	        self::_NAME => $array[self::_NAME],
	        self::_ADDRESS_ONE => $array[self::_ADDRESS_ONE],
	        self::_ADDRESS_TWO => $array[self::_ADDRESS_TWO],
	        self::_CITY => $array[self::_CITY],
	        self::_STATE => $array[self::_STATE],
	        self::_ZIP => $array[self::_ZIP],
	        self::_COUNTRY => $array[self::_COUNTRY],
	        self::_WEB_ADDRESS => $array[self::_WEB_ADDRESS],
	        self::_PHONE_NUMBER_OFFICE => $array[self::_PHONE_NUMBER_OFFICE],
	        self::_PHONE_NUMBER_FAX => $array[self::_PHONE_NUMBER_FAX],
	        self::_TIME_ZONE_ID => $array[self::_TIME_ZONE_ID],
	        self::_CAN_SEE_PRIVATE => $canSeePrivate,
            #self::_TOKEN => $array[self::_TOKEN],
            self::_URL_NAME => $array[self::_URL_NAME]
        );

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

    protected function _getVal($name)
    {
        return (isset($this->_data[$name])) ? $this->_data[$name] : null;
    }

}