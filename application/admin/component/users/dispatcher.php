<?php
/**
 * @category	Nooku
 * @package		Nooku_Server
 * @subpackage	Users
 * @copyright	Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

use Nooku\Library;

/**
 * Users Dispatcher Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @category	Nooku
 * @package		Nooku_Server
 * @subpackage	Users
 */
class UsersDispatcher extends Library\DispatcherComponent
{
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        //@TODO Remove when PHP 5.5 becomes a requirement.
        Library\ClassLoader::getInstance()->loadFile(JPATH_ROOT.'/application/admin/component/users/legacy/password.php');
    }
    
    protected function _actionDispatch(Library\CommandContext $context)
	{
        if($context->user->isAuthentic() && $context->request->isGet())
        {
            //Redirect if user is already logged in
            if($context->request->query->get('view', 'alpha') == 'session')
            {
                //@TODO : Fix the redirect
                //$context->response->setRedirect($context->request->getReferrer(), 'You are already logged in!');
                //return false;
            }
        }
       
        return parent::_actionDispatch($context);
	}
}