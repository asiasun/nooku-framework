<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Debug;

use Nooku\Framework;

/**
 * Application Event Subscriber
.*
 * @author		Johan Janssens <johan@nooku.org>
 * @package Nooku\Component\Debug
 */
class EventSubscriberApplication extends Framework\EventSubscriberAbstract
{
    public function __construct(Framework\Config $config)
	{
	    //Intercept the events for profiling
	    if($this->getService('application')->getCfg('debug'))
	    {
	        //Replace the event dispatcher
	        $this->getService()->setAlias('lib:event.dispatcher.default', 'com:debug.event.profiler');
	          
	        //Add the database tracer
	        $this->getService('application.database')->addEventSubscriber('com:debug.event.subscriber.database');
		}
		
		parent::__construct($config);
	}
}