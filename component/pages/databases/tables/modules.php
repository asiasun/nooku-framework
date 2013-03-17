<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Pages;

use Nooku\Framework;

/**
 * Modules Database Table
 *
 * @author  Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @package Nooku\Component\Pages
 */
class DatabaseTableModules extends Framework\DatabaseTableDefault
{
    public function  _initialize(Framework\Config $config)
    {
        $config->append(array(
            'behaviors'  => array(
                'creatable', 'modifiable', 'lockable',
                'com:pages.database.behavior.orderable' => array('strategy' => 'flat')
            ),
            'filters' => array(
                'content' => array('html', 'tidy'),
                'params'  => 'ini' 
            )
        ));
     
        parent::_initialize($config);
    }

	/**
	 * Get default values for all columns
	 *
	 * This method is specialized in order to set the default module position 
	 * and published state
	 * 
	 * @return  array
	 */
	public function getDefaults()
	{
		$defaults = parent::getDefaults();
		
		$defaults['position']    = 'left';
		$defaults['published']	 = 1;
		$defaults['description'] = '';
	     
		return $defaults;
	}
}