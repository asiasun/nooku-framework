<?php
/**
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright	Copyright (C) 2009 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://www.nooku.org
 */

/**
 * Object Aliases
 *
 * @author      Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package     Nooku_Server
 * @subpackage  Articles
 */

use Nooku\Library\ObjectManager;

ObjectManager::getInstance()->registerAlias('com:articles.model.categories', 'com:categories.model.categories');

