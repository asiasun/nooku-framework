<?php
/**
 * @package        Nooku_Server
 * @subpackage     Articles
 * @copyright      Copyright (C) 2009 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Alias Template Filter Class
 *
 * @author     Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package    Nooku_Server
 * @subpackage Articles
 */
class ArticlesTemplateFilterAlias extends Framework\TemplateFilterAlias
{
    public function __construct(Framework\Config $config)
    {
        parent::__construct($config);

        $this->addAlias(array('@highlight(' => '$this->getView()->highlight('), Framework\TemplateFilter::MODE_READ);
    }
}