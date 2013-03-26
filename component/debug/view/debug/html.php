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
 * Debug Html View
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Debug
 */
class ViewDebugHtml extends Framework\ViewHtml
{
    public function render()
    {
        $database = $this->getService('com:debug.event.subscriber.database');
        $profiler = $this->getService('com:debug.event.profiler');
        $language = \JFactory::getLanguage();

        //Remove the template includes
        $includes = get_included_files();

        foreach($includes as $key => $value)
        {
            //Find the real file path
            if($alias = $this->getService('loader')->getAlias($value)) {
                $includes[$key] = $alias;
            };
        }

	    $this->memory    = $profiler->getMemory();
	    $this->events    = (array) $profiler->getEvents();
	    $this->queries   = (array) $database->getQueries();
	    $this->languages = (array) $language->getPaths();
	    $this->includes  = (array) $includes;
	    $this->strings   = (array) $language->getOrphans();
                        
        return parent::render();
    }
}