<?php
/**
 * @package     Nooku_Server
 * @subpackage  Languages
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Language Controller Toolbar Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Languages
 */

class LanguagesControllerToolbarLanguage extends BaseControllerToolbarDefault
{
    public function onAfterControllerBrowse(Framework\Event $event)
    {    
        parent::onAfterControllerBrowse($event);

        $this->addSeparator();
        $this->addEnable();
        $this->addDisable();
    }
}