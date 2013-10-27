<?php
/**
 * This file is part of the CnetSync package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CnetSync\Configuration;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class Configuration
{
    /**
     * @var string
     */
    protected $apiUrl = 'http://build.uitdatabank.be/api';

    /**
     * @var string
     */
    protected $collectionType = 'events';

    /**
     * @var string
     */
    protected $method = 'xmlview';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @param  int    $page
     * @return string
     */
    public function buildApiUrl($page)
    {
        $params = $this->params;

        $params['page'] = $page;
        $params['key'] = $this->apiKey;

        return $this->apiUrl .'/' . $this->collectionType . '/' . $this->method . '?' . http_build_query($params);
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}
}
