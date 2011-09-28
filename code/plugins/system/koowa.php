<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Plugins
 * @subpackage  System
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Koowa System plugin
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Plugins
 * @subpackage  System
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemKoowa extends JPlugin
{
	public function __construct($subject, $config = array())
	{
		// Check if Koowa is active
		if(JFactory::getApplication()->getCfg('dbtype') != 'mysqli')
		{
    		JError::raiseWarning(0, JText::_("Koowa plugin requires MySQLi Database Driver. Please change your database configuration settings to 'mysqli'"));
    		return;
		}
		
		// Check for suhosin
		if(in_array('suhosin', get_loaded_extensions()))
		{
			//Attempt setting the whitelist value
			@ini_set('suhosin.executor.include.whitelist', 'tmpl://, file://');

			//Checking if the whitelist is ok
			if(!@ini_get('suhosin.executor.include.whitelist') || strpos(@ini_get('suhosin.executor.include.whitelist'), 'tmpl://') === false)
			{
				JError::raiseWarning(0, sprintf(JText::_('Your server has Suhosin loaded. Please follow <a href="%s" target="_blank">this</a> tutorial.'), 'https://nooku.assembla.com/wiki/show/nooku-framework/Known_Issues'));
				return;
			}
		}
		
	    //Safety Extender compatibility
		if(extension_loaded('safeex') && strpos('tmpl', ini_get('safeex.url_include_proto_whitelist')) === false)
		{
		    $whitelist = ini_get('safeex.url_include_proto_whitelist');
		    $whitelist = (strlen($whitelist) ? $whitelist . ',' : '') . 'tmpl';
		    ini_set('safeex.url_include_proto_whitelist', $whitelist);
 		}
		
		//Set constants
		define('KDEBUG'      , JDEBUG);
		
		//Set path definitions
		define('JPATH_FILES' , JPATH_ROOT);
		define('JPATH_IMAGES', JPATH_ROOT.DS.'images');
		
		//Set exception handler
		set_exception_handler(array($this, 'exceptionHandler'));
		
		// Koowa : setup
        require_once( JPATH_LIBRARIES.'/koowa/koowa.php');
        Koowa::getInstance();	
		
		 //Setup the loader
        KLoader::addAdapter(new KLoaderAdapterModule(JPATH_BASE));
        KLoader::addAdapter(new KLoaderAdapterPlugin(JPATH_ROOT));
        KLoader::addAdapter(new KLoaderAdapterComponent(JPATH_BASE));
		
        // Koowa : setup factory
        KIdentifier::addAdapter(new KIdentifierAdapterModule());
        KIdentifier::addAdapter(new KIdentifierAdapterPlugin());
        KIdentifier::addAdapter(new KIdentifierAdapterComponent());
		
        //Koowa : register identifier application paths
        KIdentifier::setApplication('site' , JPATH_SITE);
        KIdentifier::setApplication('admin', JPATH_ADMINISTRATOR);

        //Koowa : setup factory mappings
        KIdentifier::setAlias('koowa:database.adapter.mysqli', 'com://admin/default.database.adapter.mysqli');
		
	    //Setup the request
        KRequest::root(str_replace('/'.JFactory::getApplication()->getName(), '', KRequest::base()));
			 
		//Load the koowa plugins
		JPluginHelper::importPlugin('koowa', null, true, KFactory::get('com://admin/default.event.dispatcher'));
		
	    //Bugfix : Set offset accoording to user's timezone
		if(!JFactory::getUser()->guest) 
		{
		   if($offset = JFactory::getUser()->getParam('timezone')) {
		        JFactory::getConfig()->setValue('config.offset', $offset);
		   }
		}

		parent::__construct($subject, $config);
	}
	
	/**
	 * On after intitialse event handler
	 * 
	 * This functions implements HTTP Basic authentication support
	 * 
	 * @return void
	 */
	public function onAfterInitialise()
	{  
	    /*
	     * Try to log the user in
	     * 
	     * If the request contains authorization information we try to log the user in
	     */
	    if($this->params->get('auth_basic', 0) && JFactory::getUser()->guest) {
	        $this->_authenticateUser();
	    }
	    
	    /*
	     * Reset the user and token
	     *
	     * In case another plugin have logged in after we initialized we need to reset the token and user object
	     * One plugin that could cause that, are the Remember Me plugin
	     */
	     if(KFactory::get('joomla:user')->get('guest') && !JFactory::getUser()->guest)
	     { 
	         //Force the token
	         KRequest::set('request._token', JUtility::getToken());
	     }
	}
	
	/**
	 * On after route event handler
	 * 
	 * @return void
	 */
	public function onAfterRoute()
	{      
	    /*
	     * Special handling for AJAX requests
	     * 
	     * If the format is AJAX and the format is 'html' or the tmpl is empty we re-create 
	     * a 'raw' document rendered and force it's type to the active format
	     */
        if(KRequest::type() == 'AJAX') 
        {
        	if(KRequest::get('get.format', 'cmd', 'html') != 'html' || KRequest::get('get.tmpl', 'cmd') === '')
        	{
        		$format = JRequest::getWord('format', 'html');
        	
        		JRequest::setVar('format', 'raw');   //force format to raw
        		
        		$document =& JFactory::getDocument();
        		$document = null;
        		JFactory::getDocument()->setType($format);
        		
        		
        		JRequest::setVar('format', $format); //revert format to original
        	}
        }
        
        //Set the request format
        if(!KRequest::has('request.format')) {
            KRequest::set('request.format', KRequest::format());
        }
	}
	
 	/**
	 * Catch all exception handler
	 *
	 * Calls the Joomla error handler to process the exception
	 *
	 * @param object an Exception object
	 * @return void
	 */
	public function exceptionHandler($exception)
	{
		$this->_exception = $exception; //store the exception for later use
		
		//Change the Joomla error handler to our own local handler and call it
		JError::setErrorHandling( E_ERROR, 'callback', array($this,'errorHandler'));
		
		//Make sure we have a valid status code
		JError::raiseError(KHttpResponse::isError($exception->getCode()) ? $exception->getCode() : 500, $exception->getMessage());
	}

	/**
	 * Custom JError callback
	 *
	 * Push the exception call stack in the JException returned through the call back
	 * adn then rener the custom error page
	 *
	 * @param object A JException object
	 * @return void
	 */
	public function errorHandler($error)
	{
		$error->setProperties(array(
			'backtrace'	=> $this->_exception->getTrace(),
			'file'		=> $this->_exception->getFile(),
			'line'		=> $this->_exception->getLine()
		));
		
	    if(JFactory::getConfig()->getValue('config.debug')) {
			$error->set('message', (string) $this->_exception);
		} else {
			$error->set('message', KHttpResponse::getMessage($error->code));
		}
		
	    if($this->_exception->getCode() == KHttpResponse::UNAUTHORIZED) {
		   header('WWW-Authenticate: Basic Realm="'.KRequest::base().'"');
		}
		
		//Make sure the buffers are cleared
		while(@ob_get_clean());
		
		JError::customErrorPage($error);
	}
	
	/**
	 * Basic authentication support
	 *
	 * This functions tries to log the user in if authentication credentials are
	 * present in the request.
	 *
	 * @return boolean	Returns TRUE is basic authentication was successful
	 */
	protected function _authenticateUser()
	{
	    if(KRequest::has('server.PHP_AUTH_USER') && KRequest::has('server.PHP_AUTH_PW')) 
	    {
	        $credentials = array(
	            'username' => KRequest::get('server.PHP_AUTH_USER', 'url'),
	            'password' => KRequest::get('server.PHP_AUTH_PW'  , 'url'),
	        );
	        
	        if(JFactory::getApplication()->login($credentials) !== true) 
	        {  
	            throw new KException('Login failed', KHttpResponse::UNAUTHORIZED);
        	    return false;      
	        }
	         
	        //Force the token
	        KRequest::set('request._token', JUtility::getToken());
	        
	        return true;
	    }
	    
	    return false;
	}
}

/**
 * PHP5.3 compatibility
 */
if(false === function_exists('lcfirst'))
{
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst( $str ) {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}