<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';

/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Frontend
 * @author     Jaime Neto <contato@jaimeneto.com>
 */
class FrontZend_Module_Model_DbTable_Cache extends Zend_Cache_Core
{
    /**
     * Available options
     *
     * ====> (mixed) cached_entity :
     * - if set to a class name, we will cache an abstract class and will use 
     *   only static calls
     * - if set to an object, we will cache this object methods
     *
     * ====> (boolean) cache_by_default :
     * - if true, method calls will be cached by default
     *
     * ====> (array) cached_methods :
     * - an array of method names which will be cached 
     *   (even if cache_by_default = false)
     *
     * ====> (array) non_cached_methods :
     * - an array of method names which won't be cached 
     *   (even if cache_by_default = true)
     *
     * @var array available options
     */
    protected $_specificOptions = array(
        'cached_entity'      => null,
        'cache_by_default'   => true,
        'cached_methods'     => array(),
        'non_cached_methods' => array()
    );

    /**
     * Tags array
     *
     * @var array
     */
    private $_tags = array();

    /**
     * SpecificLifetime value
     *
     * false => no specific life time
     *
     * @var int
     */
    private $_specificLifetime = false;

    /**
     * The cached object or the name of the cached abstract class
     *
     * @var mixed
     */
    private $_cachedEntity = null;

     /**
      * The class name of the cached object or cached abstract class
      *
      * Used to differentiate between different classes with the same method 
      * calls.
      *
      * @var string
      */
    private $_cachedEntityLabel = '';

    /**
     * Priority (used by some particular backends)
     *
     * @var int
     */
    private $_priority = 8;

    /**
     * Constructor
     *
     * @param  array $options Associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct(array $options = array())
    {
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        if ($this->_specificOptions['cached_entity'] === null) {
            Zend_Cache::throwException('cached_entity must be set !');
        }
        $this->setCachedEntity($this->_specificOptions['cached_entity']);
        $this->setOption('automatic_serialization', true);
    }

    /**
     * Set a specific life time
     *
     * @param  int $specificLifetime
     * @return void
     */
    public function setSpecificLifetime($specificLifetime = false)
    {
        $this->_specificLifetime = $specificLifetime;
    }

    /**
     * Set the priority (used by some particular backends)
     *
     * @param int $priority integer between 0 (very low priority) and 
     *                      10 (maximum priority)
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;
    }

    /**
     * Public frontend to set an option
     *
     * Just a wrapper to get a specific behaviour for cached_entity
     *
     * @param  string $name  Name of the option
     * @param  mixed  $value Value of the option
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setOption($name, $value)
    {
        if ($name == 'cached_entity') {
            $this->setCachedEntity($value);
        } else {
            parent::setOption($name, $value);
        }
    }

    /**
     * Specific method to set the cachedEntity
     *
     * if set to a class name, we will cache an abstract class and will use 
     * only static calls
     * if set to an object, we will cache this object methods
     *
     * @param FrontZend_Module_Model_DbTable_Abstract $cachedEntity
     */
    public function setCachedEntity(FrontZend_Module_Model_DbTable_Abstract $cachedEntity)
    {
        $this->_cachedEntity = $cachedEntity;
        $this->_specificOptions['cached_entity'] = $cachedEntity;
        $ro = new ReflectionObject($this->_cachedEntity);
        $this->_cachedEntityLabel = $ro->getName();
        $this->_tags[] = $this->_cachedEntityLabel;
    }

    /**
     * Set the cache array
     *
     * @param  array $tags
     * @return void
     */
    public function setTagsArray($tags = array())
    {
        $this->_tags = $tags;
    }
    
    public function findById($id=null)
    {
        $cacheId = $this->makeId($id);
        if ($this->test($cacheId)) {
            $data = $this->load($cacheId);
            $data->setTable($this->_cachedEntity);
        } else {
            $tags = $this->_tags;
            $tags[] = $cacheId;
            
            $data = $this->_cachedEntity->findById($id);
            $this->save($data, $cacheId, $tags, $this->_specificLifetime, 
                    $this->_priority, true);
        }

        return $data;
    }

    public function findOne($options=null)
    {
        $cacheId = $this->makeArgsId(__METHOD__, array($options));
        if ($this->test($cacheId)) {
            $data = $this->load($cacheId);
        } else {
            $data = $this->_cachedEntity->findOne($options);

            $tags = $this->_tags;
            if (is_object($data) && method_exists($data, 'getId')) {
                $objId = $data->getId();
                $tags[] = $this->makeId($objId);

                $this->save($data, $objId, $tags, $this->_specificLifetime, 
                        $this->_priority, true);
            }
            $tags[] = $this->makeId('many');
            $this->save($data, $cacheId, $tags, $this->_specificLifetime, 
                    $this->_priority, true);
        }

        return $data;
    }

    public function findAll($options=null)
    {
        $cacheId = $this->makeArgsId(__METHOD__, array($options));
        if ($this->test($cacheId)) {
            $data = $this->load($cacheId);
        } else {
            $data = $this->_cachedEntity->findAll($options);

            $tags = $this->_tags;

            if (is_array($data) || $data instanceof Zend_Paginator) {
                foreach($data as $obj) {
                    $objTags = $tags;
                    if (is_object($obj) && method_exists($obj, 'getId')) {
                        $objId = str_replace('-', '_', $obj->getId());
                        $objTags[] = $this->makeId($objId);
                        $tags[] = $this->makeId($objId);

                        $this->save($obj, $objId, $objTags, 
                                $this->_specificLifetime, 
                                $this->_priority, true);
                    }
                }
            }
            $tags[] = $this->makeId('many');

            $this->save($data, $cacheId, $tags, $this->_specificLifetime, 
                    $this->_priority, true);
        }

        return $data;
    }

    public function fetchPairs($field, $where=null, $order=null)
    {
        $cacheId = $this->makeArgsId(__METHOD__, array($field, $where, $order));
        if ($this->test($cacheId)) {
            $data = $this->load($cacheId);
        } else {
            $data = $this->_cachedEntity->fetchPairs($field, $where, $order);

            $tags = $this->_tags;

            if (is_array($data)) {
                foreach($data as $objId => $obj) {
                    $tags[] = $this->makeId($objId);
                }
            }
            $tags[] = $this->makeId('many');

            $this->save($data, $cacheId, $tags, $this->_specificLifetime,
                    $this->_priority, true);
        }

        return $data;
    }

    public function count($where=null)
    {
        $cacheId = $this->makeArgsId(__METHOD__, array($where));
        if ($this->test($cacheId)) {
            $data = $this->load($cacheId);
        } else {
            $data = $this->_cachedEntity->count($where);
            $tags = array($this->makeId('many'));
            $this->save($data, $cacheId, $tags, $this->_specificLifetime,
                    $this->_priority, true);
        }

        return $data;
    }

    protected function _cleanCache($id)
    {
        $this->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
            array($this->makeId($id))
        );
    }

    public function save($data, $id = null, $tags = array(), 
            $specificLifetime = false, $priority = 8, $forceSaveCache=false)
    {
        if ($data instanceof FrontZend_Module_Model_Abstract
                && !$forceSaveCache) {
            $result = $this->_cachedEntity->save($data);
            if ($result) {
                $this->_cleanCache($data->getId());
                $this->_cleanCache('many');
            }
            return $result;
            
        } else {
            return parent::save($data, $id, $tags, 
                                $specificLifetime, $priority);
        }
    }

    public function insert(array $data)
    {
        $result = $this->_cachedEntity->insert($data);
        if ($result) {
            $this->_cleanCache('many');
        }
        return $result;
    }
    
    public function update(array $data, $where)
    {
        $result = $this->_cachedEntity->update($data, $where);
        if ($result) {
            $this->_cleanCache('many');
        }
        return $result;
    }

    public function delete($where)
    {
        $result = $this->_cachedEntity->delete($where);
        if ($result) {
            $this->_cleanCache('many');
        }
        return $result;
    }

    public function deleteById($id)
    {
        $result = $this->_cachedEntity->deleteById($id);
        if ($result) {
            $this->_cleanCache($id);
            $this->_cleanCache('many');
        }
        return $result;
    }

    /**
     * Main method : call the specified method or get the result from cache
     *
     * @param  string $name       Method name
     * @param  array  $parameters Method parameters
     * @return mixed Result
     */
    public function __call($name, $parameters)
    {
        $callback = array($this->_cachedEntity, $name);

        if (!is_callable($callback, false)) {
            Zend_Cache::throwException('Invalid callback');
        }
//
//        $cacheBool1 = $this->_specificOptions['cache_by_default'];
//        $cacheBool2 = in_array($name, 
//                          $this->_specificOptions['cached_methods']);
//        $cacheBool3 = in_array($name, 
//                          $this->_specificOptions['non_cached_methods']);
//        $cache = (($cacheBool1 || $cacheBool2) && (!$cacheBool3));
//        if (!$cache) {
//            // We do not have not cache
            return call_user_func_array($callback, $parameters);
//        }
//
//        $id = $this->makeId($name, $parameters);
//        if ( ($rs = $this->load($id)) && isset($rs[0], $rs[1]) ) {
//            // A cache is available
//            $output = $rs[0];
//            $return = $rs[1];
//        } else {
//            // A cache is not available (or not valid for this frontend)
//            ob_start();
//            ob_implicit_flush(false);
//
//            try {
//                $return = call_user_func_array($callback, $parameters);
//                $output = ob_get_clean();
//                $data = array($output, $return);
//                $this->save($data, $id, $this->_tags, $this->_specificLifetime, 
//                            $this->_priority);
//            } catch (Exception $e) {
//                ob_end_clean();
//                throw $e;
//            }
//        }
//
//        echo $output;
//        return $return;
    }

    /**
     * Make a cache id from the method name and parameters
     *
     * @param  string $modelId
     * @return string Cache id
     */
    public function makeId($modelId)
    {
        $modelId = str_replace('-', '_', $modelId);
        return strtolower($this->_cachedEntity->getRowClass()) . '_'
                          . $modelId;
    }

    /**
     * Make a cache id from the method name and parameters
     *
     * @param  string $name Method name
     * @param  array  $args Method parameters
     * @return string Cache id
     */
    public function makeArgsId($name, array $args = array())
    {
        return md5($this->_cachedEntityLabel . '__' . $name . '__' . 
                   serialize($args));
    }

}