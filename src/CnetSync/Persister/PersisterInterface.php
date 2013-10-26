<?php
/**
 * This file is part of the CnetSync package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CnetSync\Persister;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
interface PersisterInterface
{
	public function persist(\CultureFeed_Cdb_Item_Event $object);
}
