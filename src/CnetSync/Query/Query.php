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
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Plugin\Oauth\OauthPlugin;

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
        $this->pageSize = 100;
    }

    /**
     * @return bool
     * @throws ClientErrorResponseException
     * @throws \Exception
     */
    protected function loadItems()
    {
        try {
            $client = new Client($this->config->getApiUrl(), array(
                'curl.options' => array(
                    CURLOPT_CONNECTTIMEOUT => 5,
                )
            ));

            $oauth = new OauthPlugin(array(
                'consumer_key'    => $this->config->getConsumerKey(),
                'consumer_secret' => $this->config->getConsumerSecret(),
            ));
            $client->addSubscriber($oauth);

            $request = $client->get('');
            $request->getQuery()->set('q', $this->config->getParam('q', '*.*'));//'cdbid:406b48c4-754c-4c77-98ef-80a91a6785b1'));
            $request->getQuery()->set('fq', $this->config->getParam('fq', 'type:event'));
            $request->getQuery()->set('sort', 'title_sort+asc');
            $request->getQuery()->set('start', $this->config->getParam('start', $this->page));
            $request->getQuery()->set('rows', $this->config->getParam('rows', $this->pageSize));

            $namespace = $this->config->getParam('namespace', 'http://www.cultuurdatabank.com/XMLSchema/CdbXSD/3.2/FINAL');

            try {
                $response = $request->send();
                $responseBody = $response->getBody(true);
                $xml = simplexml_load_string($responseBody, 'SimpleXMLElement', 0, $namespace);
            }
            catch (ClientErrorResponseException $e) {
                throw $e;
            }

            $this->result = \CultureFeed_Cdb_List_Results::parseFromCdbXml($xml);
            $xml->registerXPathNamespace('cdb', $namespace);
            $this->result->setTotalResultsFound((int) count($xml->xpath('/cdb:cdbxml/cdb:event')));
        } catch (\Exception $e) {
            throw $e;
        }

        $this->page += $this->pageSize;

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
