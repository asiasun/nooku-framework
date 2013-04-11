<?php
/**
 * @package        Koowa_Service
 * @subpackage     Locator
 * @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

namespace Nooku\Library;

/**
 * Service Abstract Locator
 *
 * @author        Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 * @subpackage     Locator
 */
abstract class ServiceLocatorAbstract extends Object implements ServiceLocatorInterface
{
    /**
     * The locator type
     *
     * @var string
     */
    protected $_type = '';

    /**
     * The class prefix sequence in FIFO order
     *
     * @var array
     */
    protected $_fallbacks = array();

    /**
     * Constructor.
     *
     * @param   object  An optional Config object with configuration options
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);

        $this->_fallbacks = Config::unbox($config->fallbacks);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional Config object with configuration options.
     * @return  void
     */
    protected function _initialize(Config $config)
    {
        $config->append(array(
            'fallbacks' => array(),
        ));
    }

    /**
     * Get the type
     *
     * @return string    Returns the type
     */
    public function getType()
    {
        return $this->_type;
    }
}