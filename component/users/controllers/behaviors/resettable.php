<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Users;

use Nooku\Framework;

/**
 * Resettable Controller Behavior
 *
 * @author     Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package    Nooku_Server
 * @subpackage Users
 */
class ControllerBehaviorResettable extends Framework\ControllerBehaviorAbstract
{
    public function __construct(Framework\Config $config)
    {
        parent::__construct($config);

        //@TODO Remove when PHP 5.5 becomes a requirement.
        $this->getService('loader')->loadFile(JPATH_ROOT . '/component/users/legacy.php');
    }

    protected function _beforeControllerRead(Framework\CommandContext $context)
    {
        if ($token = $context->request->query->get('token', 'cmd'))
        {
            if ($this->_tokenValid($token))
            {
                $this->getView()->token = $token; // Push the token to the view.
            }
            else
            {
                $context->response->setRedirect($this->getService('application.pages')->getHome->url);
                //@TODO : Set message in session
                //$context->response->setRedirect($this->getService('application.pages')->getHome->url, JText::_('INVALID_TOKEN'),'error');
                return false;
            }
        }
    }

    protected function _beforeControllerReset(Framework\CommandContext $context)
    {
        $password = $this->getModel()->getRow();

        if ($this->_tokenValid($context->request->data->get('token', 'string')))
        {
            $context->password = $password;
            $result  = true;
        }
        else
        {
            $context->response->setRedirect($this->getService('application.pages')->getHome->url);
            //@TODO : Set message in session
            //$context->response->setRedirect($this->getService('application.pages')->getHome->url, JText::_('INVALID_TOKEN'),'error');

            $result = false;
        }

        return $result;
    }

    protected function _actionReset(Framework\CommandContext $context)
    {
        $password = $context->password;

        $password->password = $context->request->data->get('password', 'string');
        $password->reset    = '';
        $password->save();

        if ($password->getStatus() == Framework\Database::STATUS_FAILED)
        {
            $context->response->setRedirect($context->request->getReferrer());
            //@TODO : Set message in session
            //$context->response->setRedirect($context->request->getReferrer(), $password->getStatusMessage(), 'error');
            $result = false;
        }
        else
        {
            $context->response->setRedirect($this->getService('application.pages')->getHome()->url, JText::_('PASSWORD_RESET_SUCCESS'));
            $result = true;
        }

        return $result;
    }

    protected function _tokenValid($token)
    {
        $result   = false;
        $password = $this->getModel()->getRow();

        if ($password->reset && ($password->verify($token, $password->reset))) {
            $result = true;
        }

        return $result;
    }

    protected function _beforeControllerToken(Framework\CommandContext $context)
    {
        $user = $this->getService('com:users.model.users')
            ->set('email', $context->request->data->get('email', 'email'))
            ->getRow();

        if ($user->isNew() || !$user->enabled)
        {
            $context->response->setRedirect($context->request->getReferrer());
            //@TODO : Set message in session
            //$context->reponse->setRedirect($context->request->getReferrer(), JText::_('COULD_NOT_FIND_USER'), 'error');
            $result = false;
        }
        else
        {
            $context->user = $user;
            $result        = true;
        }

        return $result;
    }

    protected function _actionToken(Framework\CommandContext $context)
    {
        $user     = $context->user;
        $password = $user->getPassword();
        $token    = $password->setReset();

        $config     = \JFactory::getConfig();
        $site_name  = $config->getValue('sitename');
        $from_email = $config->getValue('mailfrom');
        $from_name  = $config->getValue('fromname');
        $url        = $this->getService('lib:http.url',
            array('url' => "option=com_users&view=password&layout=form&id={$password->id}&token={$token}"));
        $this->getService('application')->getRouter()->build($url);
        $url     = $url = $context->request->getUrl()->toString(Framework\HttpUrl::SCHEME | Framework\HttpUrl::HOST | Framework\HttpUrl::PORT) . $url;

        $subject = \JText::sprintf('PASSWORD_RESET_CONFIRMATION_EMAIL_TITLE', $site_name);
        $body    = \JText::sprintf('PASSWORD_RESET_CONFIRMATION_EMAIL_TEXT', $site_name, $url);

        if (!JUtility::sendMail($from_email, $from_name, $user->email, $subject, $body))
        {
            $context->response->setRedirect($context->request->getReferrer());
            //@TODO : Set message in session
            //$context->response->setRedirect($context->request->getReferrer(), JText::_('ERROR_SENDING_CONFIRMATION_EMAIL'), 'error');
            $result = false;
        }
        else
        {
            $context->response->setRedirect($this->getService('application.pages')->getHome()->url, \JText::_('CONFIRMATION_EMAIL_SENT'));
            $result = true;
        }

        return $result;
    }
}