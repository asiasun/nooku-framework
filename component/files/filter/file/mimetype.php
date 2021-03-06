<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * File Mimetype Filter
 *
 * @author  Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package Nooku\Component\Files
 */
class FilterFileMimetype extends Library\FilterAbstract
{
	public function validate($entity)
	{
		$mimetypes = Library\ObjectConfig::unbox($entity->getContainer()->getParameters()->allowed_mimetypes);

		if (is_array($mimetypes))
		{
			$mimetype = $entity->mimetype;

			if (empty($mimetype))
            {
				if (is_uploaded_file($entity->file) && $entity->isImage())
                {
					$info = getimagesize($entity->file);
					$mimetype = $info ? $info['mime'] : false;
				}
                elseif ($entity->file instanceof SplFileInfo) {
					$mimetype = $this->getObject('com:files.mixin.mimetype')->getMimetype($entity->file->getPathname());
				}
			}

			if ($mimetype && !in_array($mimetype, $mimetypes)) {
				return $this->_error(\JText::_('Invalid Mimetype'));
			}
		}
	}
}