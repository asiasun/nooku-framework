<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Authorize Command Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package     Nooku_Components
 * @subpackage  Files
 */
class FilesControllerPermissionDefault extends BaseControllerPermissionDefault
{
	protected static $_group_map = array(
		4 => 18,
		3 => 19,
		2 => 20,
		1 => 21
	);

	protected function _authorize()
	{
		$result = true;

		/*if (version_compare(JVERSION, '1.6', '<')) {
    		$minimum = $this->getMixer()->getModel()->container->parameters->allowed_media_usergroup;
    		$minimum = isset(self::$_group_map[$minimum]) ? self::$_group_map[$minimum] : 18;

    		$result = $this->getUser()->getRole() >= $minimum;
		}*/

		return $result;
	}

	public function canGet()
	{
		return $this->_authorize();
	}

	public function canPost()
	{
		return $this->_authorize();
	}

    public function canAdd()
    {
		return $this->_authorize();
	}

	public function canEdit()
    {
		return $this->_authorize();
	}

	public function canDelete()
    {
		return $this->_authorize();
	}
}