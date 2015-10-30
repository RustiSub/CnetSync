<?php
/**
 * This file is part of the CnetSync package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CnetSync\Adapter;

use CnetSync\Configuration\Configuration;
use EgoCore\Error\Exception;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Psr\Log\LoggerInterface;

/**
 * Service that takes SOLR arguments, passes them to the CNET Search API v2 and returns Cdb objects
 *
 * @author Wouter Pirotte <wouter.pirotte@gmail.com>
 */
class V2Adapter implements CnetAdapterInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $actorCache;

    /**
     * @param Configuration $config
     * @param LoggerInterface $logger
     */
    public function __construct(Configuration $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @return \CultureFeed_Cdb_List_Results
     */
    public function getEventList($page = 0, $pageSize = 100)
    {
        $request = $this->getCnetRequest();

        $this->applyPaging($page, $pageSize, $request);

        $request->getQuery()->set('q', $this->config->getParam('q', '*.*'));
        $request->getQuery()->set('fq', $this->config->getParam('fq', 'type:event'));
        $request->getQuery()->set('group', $this->config->getParam('group', 'event'));

        //TODO: sort causes a 401 Unauthorized
        //$request->getQuery()->set('sort', 'score+desc');

        try {
            $response = $request->send();
            $responseBody = $response->getBody(true);
            $xml = simplexml_load_string($responseBody, 'SimpleXMLElement', 0, $this->config->getNameSpace());

            $result = \CultureFeed_Cdb_List_Results::parseFromCdbXml($xml);
            $result = $this->completeEventList($result);

            $xml->registerXPathNamespace('cdb', $this->config->getNameSpace());
            $result->setTotalResultsFound((int) count($xml->xpath('/cdb:cdbxml/cdb:event')));
        }
        catch (ClientErrorResponseException $e) {
            throw $e;
        }

        return $result;
    }

    /**
     * @param string $cdbId
     * @return \CultureFeed_Cdb_Item_Actor|false
     * @throws \CultureFeed_Cdb_ParseException
     */
    public function getActor($cdbId)
    {
        $request = $this->getCnetRequest();

        $request->getQuery()->set('q', 'cdbid:' . $cdbId);
        $request->getQuery()->set('group', 'actor');

        try {
            $response = $request->send();
            $responseBody = $response->getBody(true);
            $xml = simplexml_load_string($responseBody, 'SimpleXMLElement', 0, $this->config->getNameSpace());

            $actors = array();

            foreach ($xml->actor as $actorXml) {
                if ($actor = \CultureFeed_Cdb_Item_Actor::parseFromCdbXml($actorXml)) {
                    $actors[] = $actor;
                }
            }

            if (count($actors) != 1) {
                $this->logger->warning(t('More than one or no Actor found for @cdbid, count: @count', array(
                    '@cdbid' => $cdbId,
                    '@count' => count($actors)
                )));

                return false;
            }

            return $actors[0];
        }
        catch (ClientErrorResponseException $e) {
            throw $e;
        }
    }

    /**
     * @return \Guzzle\Http\Message\RequestInterface
     */
    private function getCnetRequest()
    {
        $client = new Client($this->config->getApiUrl(), array(
            'curl.options' => array(
                CURLOPT_CONNECTTIMEOUT => 5,
            )
        ));

        $oauth = new OauthPlugin(array(
            'consumer_key' => $this->config->getConsumerKey(),
            'consumer_secret' => $this->config->getConsumerSecret(),
        ));
        $client->addSubscriber($oauth);

        $request = $client->get('');

        return $request;
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @param \Guzzle\Http\Message\RequestInterface $request
     */
    private function applyPaging($page, $pageSize, $request)
    {
        $request->getQuery()->set('start', $this->config->getParam('start', $page));
        $request->getQuery()->set('rows', $pageSize);
    }

    /**
     * @param \CultureFeed_Cdb_List_Results $list
     * @return \CultureFeed_Cdb_List_Results
     */
    private function completeEventList(\CultureFeed_Cdb_List_Results $list)
    {
        /** @var \CultureFeed_Cdb_Item_Event $item */
        foreach ($list as $item) {
//            var_dump($item->getCdbId());
//            /** @var \CultureFeed_Cdb_Data_Detail $detail */
//            foreach ($item->getDetails() as $detail) {
//                var_dump($detail->getTitle());
//            }

            $this->completeActor($item->getLocation());
            $this->completeActor($item->getOrganiser());
        }

        return $list;
    }

    /**
     * @param \CultureFeed_Cdb_Data_Organiser|\CultureFeed_Cdb_Data_Location $item
     * @throws \EgoCore\Error\Exception
     */
    private function completeActor($item)
    {
        if ($item && !$item->getActor() && $actorCdbId = $item->getCdbid()) {
            if (isset($actorCache[$actorCdbId])) {
                $actor = $actorCache[$actorCdbId];
            } else if ($actor = $this->getActor($actorCdbId)) {
                $actorCache[$actorCdbId] = $actor;
            }

            if ($actor) {
                $item->setActor($actor);
            }
        }
    }
}
