<?php

/*
 * This file is part of the Basecamp Classic API Wrapper for PHP 5.3+ package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Basecamp\Message;

use Sirprize\Basecamp\Id;
use Sirprize\Basecamp\Service;
use Sirprize\Basecamp\Exception;

/**
 * Represent and modify a message
 */
class Entity
{

	const _ID = 'id';
	const _TITLE = 'title';
	const _BODY = 'body';
	const _DISPLAY_BODY = 'display-body';
	const _POSTED_ON = 'posted-on';
	const _COMMENTED_AT = 'commented-at';
	const _PROJECT_ID = 'project-id';
	const _CATEGORY_ID = 'category-id';
	const _AUTHOR_ID = 'author-id';
	const _AUTHOR_NAME = 'author-name';
	const _MILESTONE_ID = 'milestone-id';
	const _COMMENTS_COUNT = 'comments-count';
	const _USE_TEXTILE = 'use-textile';
	const _ATTACHMENTS_COUNT = 'attachments-count';
	const _ATTACHMENTS = 'attachments';
	const _PRIVATE = 'private';

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

    public function getId()
    {
        return $this->_getVal(self::_ID);
    }

    public function getTitle()
    {
        return $this->_getVal(self::_TITLE);
    }
    
    public function getBody()
    {
        return $this->_getVal(self::_BODY);
    }
    
    public function getDisplayBody()
    {
        return $this->_getVal(self::_DISPLAY_BODY);
    }
    
    public function getPostedOn()
    {
        return $this->_getVal(self::_POSTED_ON);
    }
    
    public function getCommentedAt()
    {
        return $this->_getVal(self::_COMMENTED_AT);
    }
    
    public function getProjectId()
    {
        return $this->_getVal(self::_PROJECT_ID);
    }
    
    public function getCategoryId()
    {
        return $this->_getVal(self::_CATEGORY_ID);
    }
    
    public function getAuthorId()
    {
        return $this->_getVal(self::_AUTHOR_ID);
    }
    
    public function getAuthorName()
    {
        return $this->_getVal(self::_AUTHOR_NAME);
    }
    
    public function getMilestoneId()
    {
        return $this->_getVal(self::_MILESTONE_ID);
    }
    
    public function getCommentsCount()
    {
        return $this->_getVal(self::_COMMENTS_COUNT);
    }
    
    public function getUseTextile()
    {
        return $this->_getVal(self::_USE_TEXTILE);
    }
    
    public function getAttachmentCount()
    {
        return $this->_getVal(self::_ATTACHMENTS_COUNT);
    }
    
    public function getAttachments()
    {
        if($this->_attachments === null)
        {
            $this->_attachments = $this->_getService()->getAttachmentsInstance();
        }

        return $this->_attachments;
    }
    
    public function getIsPrivate()
    {
        return $this->_getVal(self::_PRIVATE);
    }


    /**
     * Load data returned from an api request
     *
     * @throws \Sirprize\Basecamp\Exception
     * @return \Sirprize\Basecamp\Message
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
        
        if(isset($array[self::_ATTACHMENTS]))
        {
            $this->getAttachments()->load($array[self::_ATTACHMENTS]);
            $this->_attachmentsLoaded = true;
        }

        $id = new Id($array[self::_ID]);
        $projectId = new Id($array[self::_PROJECT_ID]);
        $categoryId = new Id($array[self::_CATEGORY_ID]);
        $authorId = new Id($array[self::_AUTHOR_ID]);
        $milestoneId = new Id($array[self::_MILESTONE_ID]);

        if (isset($array[self::_USE_TEXTILE]))
        {
          $useTextile = ($array[self::_USE_TEXTILE] == 'true');
        }
        else
        {
          $useTextile = false;
        }
        
        if (isset($array[self::_PRIVATE]))
        {
          $private = ($array[self::_PRIVATE] == 'true');
        }
        else
        {
          $private = false;
        }

        $this->_data = array(
	        self::_ID => $id,
			self::_TITLE => $array[self::_TITLE],
			self::_BODY => $array[self::_BODY],
			self::_DISPLAY_BODY => $array[self::_DISPLAY_BODY],
			self::_POSTED_ON => $array[self::_POSTED_ON],
			self::_COMMENTED_AT => $array[self::_COMMENTED_AT],
			self::_PROJECT_ID => $projectId,
			self::_CATEGORY_ID => $categoryId,
			self::_AUTHOR_ID => $authorId,
			self::_AUTHOR_NAME => $array[self::_AUTHOR_NAME],
			self::_MILESTONE_ID => $milestoneId,
			self::_COMMENTS_COUNT => $array[self::_COMMENTS_COUNT],
			self::_USE_TEXTILE => $useTextile,
			self::_ATTACHMENTS_COUNT => $array[self::_ATTACHMENTS_COUNT],
			self::_PRIVATE = $private
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