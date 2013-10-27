<?php
/**
 * This file is part of the CnetSync package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CnetSync\Query;

use CnetSync\Configuration\Configuration;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class Query implements \Iterator
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var \CultureFeed_Cdb_List_Results
     */
    protected $result;

    /**
     * @var int
     */
    protected $page;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->page = 0;
    }

    /**
     * Load the items
     *
     * @return bool
     */
    protected function loadItems()
    {
        try {
            //TODO use guzzle
            $xml = simplexml_load_file($this->buildUrl());

            $this->result = \CultureFeed_Cdb_List_Results::parseFromCdbXml($xml);
        } catch (\Exception $e) {

        }

        return (bool) $this->result->getTotalResultsfound();
    }

    /**
     * Build the api call url
     * NOTE: Calling this method will increase the page by one every call.
     *
     * @return string
     */
    public function buildUrl()
    {
        $page = $this->page;
        $this->page++;

        return $this->config->buildApiUrl($page);
    }

    /**
     * Return the current element
     *
     * @return null|\CultureFeed_Cdb_Item_Event Can return any type.
     */
    public function current()
    {
        if (!isset($this->result) || !$this->result->valid()) {
            if (!$this->loadItems()) {
                return false;
            }
        }

        return $this->result->current() ? $this->result->current() : false;
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->result->next();
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        $this->result->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->result->valid() || $this->loadItems();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if (!$this->result) {
            $this->loadItems();
        }
        $this->result->rewind();
    }
}
