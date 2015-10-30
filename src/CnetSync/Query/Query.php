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

use CnetSync\Adapter\CnetAdapterInterface;
use CnetSync\Configuration\Configuration;
use CnetSync\Adapter\Service;
use Guzzle\Http\Exception\ClientErrorResponseException;

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
     * @var CnetAdapterInterface
     */
    private $cnetAdapter;

    /**
     * @param Configuration $config
     * @param CnetAdapterInterface $cnetAdapter
     */
    public function __construct(Configuration $config, CnetAdapterInterface $cnetAdapter)
    {
        $this->config = $config;
        $this->page = 0;
        $this->pageSize = 1000;
        $this->maxResults = 10000;

        $this->cnetAdapter = $cnetAdapter;
    }

    /**
     * @return bool
     * @throws ClientErrorResponseException
     * @throws \Exception
     */
    protected function loadItems()
    {
        $this->maxResults = $this->config->getParam('maxResults', $this->maxResults);
        $this->pageSize = $this->config->getParam('rows', $this->pageSize);

        if ($this->page >= $this->maxResults) {
            return false;
        }

        try {
            $this->result = $this->cnetAdapter->getEventList($this->page, $this->pageSize);
        } catch (\Exception $e) {
            throw $e;
        }

        $this->page += $this->pageSize;

        return (bool) $this->result->getTotalResultsfound();
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
