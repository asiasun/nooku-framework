<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Extensions;

use Nooku\Framework;

/**
 * Setting Controller Toolbar
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Extensions
 */
class ControllerToolbarSetting extends \BaseControllerToolbarDefault
{  
    public function onAfterControllerBrowse(Framework\Event $event)
    {
        parent::onAfterControllerBrowse($event);
        
        $this->addSave();
		$this->addApply();
	    $this->addCancel(array('attribs' => array('data-novalidate' => 'novalidate')));
    }
}