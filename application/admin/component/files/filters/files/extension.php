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
 * File Extension Filter Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package     Nooku_Components
 * @subpackage  Files
 */

class FilesFilterFileExtension extends Framework\FilterAbstract
{
	protected $_walk = false;

	protected function _validate($context)
	{
		$allowed = $context->getSubject()->container->parameters->allowed_extensions;
		$value = $context->getSubject()->extension;

		if (is_array($allowed) && (empty($value) || !in_array(strtolower($value), $allowed))) {
			$context->setError(JText::_('Invalid file extension'));
			return false;
		}
	}

	protected function _sanitize($value)
	{
		return false;
	}
}