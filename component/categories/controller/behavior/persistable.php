<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */


namespace Nooku\Component\Categories;

use Nooku\Framework;

/**
 * Persistable Controller Behavior
 *
 * @author  John Bell <http://nooku.assembla.com/profile/johnbell>
 * @package Nooku\Component\Categories
 */
class ControllerBehaviorPersistable extends Framework\ControllerBehaviorPersistable
{ 
	/**
	 * Load the model state from the request
	 *
	 * This functions merges the request information with any model state information
	 * that was saved in the session and returns the result.
	 *
	 * @param 	Framework\CommandContext		The active command context
	 * @return 	void
	 */
	protected function _beforeControllerBrowse(Framework\CommandContext $context)
	{
		 // Built the session identifier based on the action
        $identifier  = $this->getModel()->getIdentifier().'.'.$this->_action.'.'.$this->getModel()->get('table');
        $state       = $context->user->get($identifier);

        //Add the data to the request query object
        $context->request->add($state);

        //Push the request query data in the model
        $this->getModel()->set($context->request->query->toArray());
	}
	
	/**
	 * Saves the model state in the session
	 *
	 * @param 	Framework\CommandContext		The active command context
	 * @return 	void
	 */
	protected function _afterControllerBrowse(Framework\CommandContext $context)
	{
		$model  = $this->getModel();
        $state  = $model->getState();

        // Built the session identifier based on the action
        $identifier  = $model->getIdentifier().'.'.$this->_action.'.'.$this->getModel()->get('table');
        
        //Set the state in the user session
        $context->user->set($identifier, $state->toArray());
	}
}