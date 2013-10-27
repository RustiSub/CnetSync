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

use CnetSync\Mock\MockPersister;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class CnetSyncTest extends \PHPUnit_Framework_TestCase
{
	public function testExample()
	{
		$config = new \CnetSync\Configuration\Configuration();
		$config->setApiKey('FCA9D466-7F85-43F2-872B-2EF78F3D6889');
		$config->setParam('regio', 'Leuven');
		$sync = new \CnetSync\CnetSync($config);
		$sync->setPersister(new MockPersister());

		$this->assertTrue($sync->run());
	}
}
