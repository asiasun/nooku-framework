<?php
/**
* @version      $Id$
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template Write Filter Interface
 *
 * Process the template on output
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Template
 * @subpackage  Filter 
 */
interface KTemplateFilterWrite
{
    /**
     * Parse the text and filter it
     *
     * @param string Block of text to parse
     * @return KTemplateFilterWrite
     */
    public function write(&$text);
}