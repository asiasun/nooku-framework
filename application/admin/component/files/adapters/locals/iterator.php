<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

class FilesAdapterLocalIterator extends Framework\Object
{
	public function getFiles(array $config = array())
	{
		$config['type'] = 'files';
		return self::getNodes($config);
	}

	public function getFolders(array $config = array())
	{
		$config['type'] = 'folders';
		return self::getNodes($config);
	}

	public function getNodes(array $config = array())
	{
		$config['path'] = $this->getService('com://admin/files.adapter.local.folder',
					array('path' => $config['path']))->getRealPath();

		try {
			$results = FilesIteratorDirectory::getNodes($config);
		}
		catch (Exception $e) {
			return false;
		}

		foreach ($results as &$result) {
			$result = rawurldecode($result);
		}
		return $results;
	}
}