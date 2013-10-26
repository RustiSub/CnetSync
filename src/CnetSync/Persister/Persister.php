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
class Persister implements PersisterInterface
{
	/**
	 * @var \CultureFeed_Cdb_Item_Event
	 */
	protected $cnetEvent;

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @param \CultureFeed_Cdb_Item_Event $event
	 */
	public function persist(\CultureFeed_Cdb_Item_Event $event)
	{
		$this->prepare($event);
		$this->persistLocation($event->getLocation());
		$this->persistContactInfo($event->getContactInfo());
		$this->persistCalendar($event->getCalendar());
		$this->persistDetails($event->getDetails());
		$this->finalize();
	}

	/**
	 *
	 */
	protected function prepare(\CultureFeed_Cdb_Item_Event $event)
	{
		$this->data = array();
		$this->cnetEvent = $event;
	}

	/**
	 * @param \CultureFeed_Cdb_Data_Location $location
	 */
	protected function persistLocation(\CultureFeed_Cdb_Data_Location $location)
	{

	}

	/**
	 * @param \CultureFeed_Cdb_Data_ContactInfo $contactInfo
	 */
	protected function persistContactInfo(\CultureFeed_Cdb_Data_ContactInfo $contactInfo)
	{

	}

	/**
	 * @param \CultureFeed_Cdb_Data_Calendar $calendar
	 */
	protected function persistCalendar(\CultureFeed_Cdb_Data_Calendar $calendar)
	{

	}

	/**
	 * @param \CultureFeed_Cdb_Data_DetailList $details
	 */
	protected function persistDetails(\CultureFeed_Cdb_Data_DetailList $details)
	{

	}

	/**
	 *
	 */
	protected function finalize()
	{

	}
}
