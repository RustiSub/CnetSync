<?php
/**
 * This file is part of the CnetSync package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CnetSync;

use CnetSync\Configuration\Configuration;
use CnetSync\Persister\PersisterInterface;
use CnetSync\Query\Query;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CnetSync
{
	/**
	 * @var Configuration
	 */
	protected $config;

	/**
	 * @var Query
	 */
	protected $query;

	/**
	 * @var PersisterInterface
	 */
	protected $persister;

	/**
	 * @param Configuration $config
	 */
	public function __construct(Configuration $config)
	{
		$this->config = $config;
		$this->query = new Query($config);
	}

	/**
	 * @param PersisterInterface $persister
	 */
	public function setPersister(PersisterInterface $persister)
	{
		$this->persister = $persister;
	}

	/**
	 * @return bool
	 */
	public function run()
	{
		foreach ($this->query as $event) {
			if (is_object($event)) {
				$this->persister->persist($event);
			}
		}

		return true;
	}
}
