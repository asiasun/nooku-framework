<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * Page Toolbar
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Application
 */
class ApplicationControllerToolbarToolbar extends Library\ControllerToolbarAbstract
{
    public function onBeforeControllerRender(Library\Event $event)
    {
        $event->getTarget()->getView()->toolbar = $this;
        $this->addCommand('profile');
        $this->addCommand('logout');
    }

    protected function _commandProfile(Library\ControllerToolbarCommand $command)
    {
        $command->href = 'option=com_users&view=user&id='.$this->getController()->getUser()->getId();
    }

    protected function _commandLogout(Library\ControllerToolbarCommand $command)
    {
        $controller = $this->getController();
        $session    = $controller->getUser()->getSession();

        $url = 'option=com_users&view=session&id='.$session->getId();
        $url = $controller->getView()->getRoute($url);

        //Form configuration
        $config = "{method:'post', url:'".$url."', params:{_action:'delete', _token:'".$session->getToken()."'}}";

        $command->append(array(
            'attribs' => array(
                'onclick'    => 'new Koowa.Form('.$config.').submit();',
                'data-action' => 'delete',
            )
        ));
    }
}