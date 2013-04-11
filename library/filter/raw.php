<?php
/**
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

namespace Nooku\Library;

/**
 * Raw filter
 *
 * Always validates and returns the raw variable
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class FilterRaw extends FilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Variable to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return true;
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  Variable to be sanitized
     * @return  mixed
     */
    protected function _sanitize($value)
    {
        return $value;
    }
}