<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Pages;

use Nooku\Library;

/**
 * Module Controller Toolbar
 *
 * @author  Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @package Nooku\Component\Pages
 */
class PagesControllerToolbarModule extends Library\ControllerToolbarModel
{
    public function onAfterControllerBrowse(Library\Event $event)
    {
        parent::onAfterControllerBrowse($event);
        
        $this->addSeparator();
        $this->addEnable(array('label' => 'publish', 'attribs' => array('data-data' => '{published:1}')));
        $this->addDisable(array('label' => 'unpublish', 'attribs' => array('data-data' => '{published:0}')));
    }
    
    protected function _commandNew(Library\ControllerToolbarCommand &$command)
    {
        $command = $this->getCommand('dialog', array('label' => 'new'));
        $command->href = 'option=com_pages&view=modules&layout=list&installed=1&tmpl=dialog';
    }
}