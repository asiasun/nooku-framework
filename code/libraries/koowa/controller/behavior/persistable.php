<?php
/**
 * @version		$Id$
 * @package		Koowa_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Persistable Controller Behavior Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @subpackage	Behavior
 */
class KControllerBehaviorPersistable extends KControllerBehaviorAbstract
{
	/**
	 * Load the model state from the request and persist it.
	 *
	 * This functions merges the request information with any model state information that was saved in the session
     * and returns the result.
	 *
	 * @param 	KCommandContext $context The active command context
	 * @return 	void
	 */
	protected function _beforeControllerBrowse(KCommandContext $context)
	{
		// Built the session identifier based on the action
		$identifier  = $this->getModel()->getIdentifier().'.'.$context->action;
		$state       = $context->user->session->get($identifier, array());

		//Append the data to the request object
		$context->request->query->add($state);

		//Push the request in the model
		$this->getModel()->set($context->request->query->toArray());
	}

	/**
	 * Saves the model state in the session.
	 *
	 * @param 	KCommandContext $context The active command context
	 * @return 	void
	 */
	protected function _afterControllerBrowse(KCommandContext $context)
	{
		$model  = $this->getModel();
		$state  = $model->getState();
		
	    $vars = array();
	    foreach($state->getStates() as $var)
	    {
	        if(!$var->unique) {
	            $vars[$var->name] = $var->value;
	        }
	    }

		// Built the session identifier based on the action
		$identifier  = $model->getIdentifier().'.'.$context->action;

        //Prevent unused state information from being persisted
        $context->user->session->remove($identifier);

        //Set the state in the session
        $context->user->session->set($identifier, $vars);
	}
}