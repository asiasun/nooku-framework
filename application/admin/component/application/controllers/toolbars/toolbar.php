<?php
/**
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Page Toolbar
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Application
 */
class ApplicationControllerToolbar extends Framework\ControllerToolbarAbstract
{
    public function onBeforeControllerRender(Framework\Event $event)
    {
        $event->getTarget()->getView()->toolbar = $this;

        $this->addCommand('preview');
        $this->addCommand('profile');
        $this->addCommand('logout');
    }

    protected function _commandPreview(Framework\ControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs' => array(
                'target' => '_blank',
            )
        ));

        $command->href = JURI::root();
    }

    protected function _commandProfile(Framework\ControllerToolbarCommand $command)
    {
        $command->href = 'option=com_users&view=user&id='.$this->getController()->getUser()->getId();
    }

    protected function _commandLogout(Framework\ControllerToolbarCommand $command)
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