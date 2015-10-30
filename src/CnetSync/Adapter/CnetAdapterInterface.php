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
use Psr\Log\LoggerInterface;


/**
 *  Interface for retrieving items from the CNET service.
 *
 * @author Wouter Pirotte <wouter.pirotte@gmail.com>
 */
interface CnetAdapterInterface
{
    /**
     * @param Configuration $config
     * @param LoggerInterface $logger
     */
    public function __construct(Configuration $config, LoggerInterface $logger);

    /**
     * @param int $page
     * @param int $pageSize
     * @return \CultureFeed_Cdb_List_Results
     */
    public function getEventList($page = 0, $pageSize = 100);

    /**
     * @param string $cdbId
     * @return \CultureFeed_Cdb_Item_Actor|false
     * @throws \CultureFeed_Cdb_ParseException
     */
    public function getActor($cdbId);
}
