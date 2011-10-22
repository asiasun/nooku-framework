<?php
/**
 * @version		$Id$
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Logs
 * @copyright	Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Logs Html View Class
 *
 * @author      Israel Canasa <http://nooku.assembla.com/profile/israelcanasa>
 * @category	Nooku
 * @package    	Nooku_Components
 * @subpackage 	Logs
 */

class ComActivitiesViewLogsHtml extends ComDefaultViewHtml
{
	public function display()
	{
		if ($this->getLayout() == 'default') 
		{
			$model = $this->getService($this->getModel()->getIdentifier());

			$this->assign('packages', $model
				->distinct(true)
				->column('package')
				->getList()
			);

			$this->assign('actions', $model
				->distinct(true)
				->column('action')
				->getList()
			);
		} 
		
		return parent::display();
	}
}