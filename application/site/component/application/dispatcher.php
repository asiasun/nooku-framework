<?php
/**
 * @package     Nooku_Server
 * @subpackage  Application
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Application Dispatcher Class
.*
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Application
 */
class ApplicationDispatcher extends Framework\DispatcherApplication
{
    /**
     * The site identifier.
     *
     * @var string
     */
    protected $_site;

    /**
     * The application message queue.
     *
     * @var	array
     */
    protected $_message_queue = array();

    /**
     * The application options
     *
     * @var Framework\Config
     */
    protected $_options = null;

    /**
     * The pathway object
     *
     * @var object
     */
    protected $_pathway;

    /**
     * Constructor.
     *
     * @param 	object 	An optional Framework\Config object with configuration options.
     */
    public function __construct(Framework\Config $config)
    {
        parent::__construct($config);

        //Register the default exception handler
        $this->addEventListener('onException', array($this, 'exception'), Framework\Event::PRIORITY_LOW);

        //Set callbacks
        $this->registerCallback('before.dispatch', array($this, 'authorizeRequest'));

        $this->registerCallback('before.run', array($this, 'loadConfig'));
        $this->registerCallback('before.run', array($this, 'loadSession'));
        $this->registerCallback('before.run', array($this, 'loadLanguage'));

        // Set the connection options
        $this->_options = $config->options;

        //Set the base url in the request
        $this->getRequest()->setBaseUrl($config->base_url);

        //Setup the request
        Framework\Request::root(str_replace('/site', '', Framework\Request::base()));

        //Set the site name
        if(empty($config->site)) {
            $this->_site = $this->_findSite();
        } else {
            $this->_site = $config->site;
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional Framework\Config object with configuration options.
     * @return 	void
     */
    protected function _initialize(Framework\Config $config)
    {
        $config->append(array(
            'base_url'          => '/',
            'event_dispatcher'  => 'com://admin/debug.event.dispatcher.debug',
            'event_subscribers' => array('com://site/application.event.subscriber.unauthorized'),
            'site'      => null,
            'options'   => array(
                'session_name' => 'site',
                'config_file'  => JPATH_ROOT.'/config/config.php',
                'language'     => null,
                'theme'        => 'bootstrap'
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Authorize the request
     *
     * @param Framework\CommandContext $context	A command context object
     */
    public function authorizeRequest(Framework\CommandContext $context)
    {
        $user = $context->user;

        if(!($this->getCfg('offline') && !$user->isAuthentic()))
        {
            $page = $context->request->query->get('Itemid', 'int');

            if(!$this->getService('application.pages')->isAuthorized($page, $user))
            {
                if(!$user->isAuthentic()) {
                    throw new Framework\ControllerExceptionUnauthorized('ALERTNOTAUTH');
                } else {
                    throw new Framework\ControllerExceptionForbidden('ALERTNOTAUTH');
                }
            }
        }
        else throw new Framework\ControllerExceptionUnauthorized('ALERTNOTAUTH');
    }

    /**
     * Run the application
     *
     * @param Framework\CommandContext $context	A command context object
     */
    protected function _actionRun(Framework\CommandContext $context)
    {
        //Set the site error reporting
        $this->getEventDispatcher()->setDebugMode($this->getCfg('debug_mode'));

        //Set the paths
        $params = $this->getService('application.components')->files->params;

        define('JPATH_FILES'  , JPATH_SITES.'/'.$this->getSite().'/files');
        define('JPATH_IMAGES' , JPATH_SITES.'/'.$this->getSite().'/files/'.$params->get('image_path', 'images'));
        define('JPATH_CACHE'  , $this->getCfg('cache_path', JPATH_ROOT.'/cache'));

        // Set timezone to user's setting, falling back to global configuration.
        $timezone = new \DateTimeZone($context->user->get('timezone', $this->getCfg('timezone')));
        date_default_timezone_set($timezone->getName());

        //Route the request
        $this->route();
    }

    /**
     * Route the request
     *
     * @param Framework\CommandContext $context	A command context object
     */
    protected function _actionRoute(Framework\CommandContext $context)
    {
        $url = clone $context->request->getUrl();

        //Parse the route
        $this->getRouter()->parse($url);

        $pages = $this->getService('application.pages');

        //Redirect the default page
        if(!$context->request->isAjax())
        {
            // Get the route based on the path
            $search = array($context->request->getBasePath(), $this->getSite());
            $route  = trim(str_replace($search, '', $url->toString(Framework\HttpURL::PATH + Framework\HttpURL::FORMAT)), '/');

            //Redirect to the default menu item if the route is empty
            if(strpos($route, $pages->getHome()->route) === 0 && $context->request->getFormat() == 'html')
            {
                $url = $pages->getHome()->getLink();
                $url->query['Itemid'] = $pages->getHome()->id;

                $this->getRouter()->build($url);

                $context->response->setRedirect($url, Framework\HttpResponse::MOVED_PERMANENTLY);
                $this->send($context);
            }
        }

        // Redirect if page type is redirect.
        if(($page = $pages->getActive()) && $page->type == 'redirect')
        {
            if($page->link_id)
            {
                $redirect = $pages->getPage($page->link_id);
                $url      = $redirect->type == 'url' ? $redirect->link_url : $redirect->route;
            }
            else $url = $page->link_url;

            $context->response->setRedirect($url, Framework\HttpResponse::MOVED_PERMANENTLY);
            $this->send($context);
        }

        //Set the request
        $context->request->query->add($url->query);

        //Set the controller to dispatch
        if($context->request->query->has('option'))
        {
            $component = substr( $context->request->query->get('option', 'cmd'), 4);
            $this->setComponent($component);
        }

        //Dispatch the request
        $this->dispatch();
    }

    /**
     * Dispatch the request
     *
     * @param Framework\CommandContext $context	A command context object
     */
    protected function _actionDispatch(Framework\CommandContext $context)
    {
        $component = $this->getController()->getIdentifier()->package;

        if (!$this->getService('application.components')->isEnabled($component)) {
            throw new Framework\ControllerExceptionNotFound('Component Not Enabled');
        }

        //Dispatch the controller
        parent::_actionDispatch($context);

        //Render the page
        if(!$context->response->isRedirect() && $context->request->getFormat() == 'html')
        {
            //Render the page
            $config = array('response' => $context->response);

            $this->getService('com://site/application.controller.page', $config)
                  ->layout($context->request->query->get('tmpl', 'cmd', 'default'))
                  ->render();
        }

        //Send the response
        $this->send($context);
    }

    /**
     * Render an exception
     *
     * @throws InvalidArgumentException If the action parameter is not an instance of Framework\Exception
     * @param Framework\CommandContext $context	A command context object
     */
    protected function _actionException(Framework\CommandContext $context)
    {
        //Check an exception was passed
        if(!isset($context->param) && !$context->param instanceof Framework\Exception)
        {
            throw new \InvalidArgumentException(
                "Action parameter 'exception' [Framework\Exception] is required"
            );
        }

        $config = array('request'  => $this->getRequest());
        $config = array('response' => $this->getResponse());

        $this->getService('com://admin/application.controller.exception',  $config)
            ->render($context->param->getException());

        //Send the response
        $this->send($context);
    }

    /**
     * Load the configuration
     *
     * @param Framework\CommandContext $context	A command context object
     * @return	void
     */
    public function loadConfig(Framework\CommandContext $context)
    {
        // Check if the site exists
        if($this->getService('com://admin/sites.model.sites')->getRowset()->find($this->getSite()))
        {
            //Load the application config settings
            JFactory::getConfig()->loadArray($this->_options->toArray());

            //Load the global config settings
            require_once( $this->_options->config_file );
            JFactory::getConfig()->loadObject(new JConfig());

            //Load the site config settings
            require_once( JPATH_SITES.'/'.$this->getSite().'/config/config.php');
            JFactory::getConfig()->loadObject(new JSiteConfig());
        }
        else throw new Framework\ControllerExceptionNotFound('Site :'.$this->getSite().' not found');
    }

    /**
     * Load the user session or create a new one
     *
     * Old sessions are flushed based on the configuration value for the cookie lifetime. If an existing session, then
     * the last access time is updated. If a new session, a session id is generated and a record is created in the
     * #__users_sessions table.
     *
     * @param Framework\CommandContext $context	A command context object
     * @return	void
     */
    public function loadSession( Framework\CommandContext $context )
    {
        $session = $context->user->session;

        //Set Session Name
        $session->setName(md5($this->getCfg('secret').$this->getCfg('session_name')));

        //Set Session Lifetime
        $session->setLifetime($this->getCfg('lifetime', 15) * 60);

        //Set Session Handler
        $session->setHandler('database', array('table' => 'com://admin/users.database.table.sessions'));

        //Set Session Options
        $session->setOptions(array(
            'cookie_path'   => (string) Framework\Request::base(),
            'cookie_secure' => $this->getCfg('force_ssl') == 2 ? true : false
        ));

        //Auto-start the session if a cookie is found
        if(!$session->isActive())
        {
            if ($context->request->cookies->has($session->getName())) {
                $session->start();
            }
        }

        //Re-create the session if we changed sites
        if($context->user->isAuthentic() && ($session->site != $this->getSite()))
        {
            //@TODO : Fix this
            //if(!$this->getService('com://admin/users.controller.session')->add()) {
            //    $session->destroy();
            //}
        }
    }

    /**
     * Load the application language
     *
     * @param Framework\CommandContext $context	A command context object
     * @return	void
     */
    public function loadLanguage(Framework\CommandContext $context)
    {
        $languages = $this->getService('application.languages');
        $language = null;

        // If a language was specified it has priority.
        if($iso_code = $this->_options->language)
        {
            $result = $languages->find(array('iso_code' => $iso_code));
            if(count($result) == 1) {
                $language = $result->top();
            }
        }

        // Otherwise use user language setting.
        if(!$language && $iso_code = $context->user->get('language'))
        {
            $result = $languages->find(array('iso_code' => $iso_code));
            if(count($result) == 1) {
                $language = $result->top();
            }
        }

        // If language still not set, use the primary.
        if(!$language) {
            $language = $languages->getPrimary();
        }

        $languages->setActive($language);

        // TODO: Remove this.
        JFactory::getConfig()->setValue('config.language', $language->iso_code);
    }

    /**
     * Get the application router.
     *
     * @param  array $options 	An optional associative array of configuration options.
     * @return	\ApplicationRouter
     */
    public function getRouter(array $options = array())
    {
        $router = $this->getService('com://site/application.router', $options);
        return $router;
    }

    /**
     * Return a reference to the application pathway object
     *
     * @return object ApplicationConfigPathway
     */
    public function getPathway()
    {
        if(!isset($this->_pathway))
        {
            // TODO: Find out why loader tries to load the admin class.
            $this->getService('loader')->loadFile(dirname(__DIR__).'/application/configs/pathway.php');

            $pathway = new ApplicationConfigPathway();
            $pages   = $this->getService('application.pages');

            if($active = $pages->getActive())
            {
                $home = $pages->getHome();
                if($active->id != $home->id)
                {
                    foreach(explode('/', $active->path) as $id)
                    {
                        $page = $pages->getPage($id);
                        switch($page->type)
                        {
                            case 'pagelink':
                            case 'url' :
                                $url = $page->getLink();
                                break;

                            case 'separator':
                                $url = null;
                                break;

                            default:
                                $url = $page->getLink();
                                $url->query['Itemid'] = $page->id;
                                $this->getRouter()->build($url);
                                break;
                        }

                        $pathway->addItem($page->title, $url);
                    }
                }
            }

            $this->_pathway = $pathway;
        }

        return $this->_pathway;
    }

    /**
     * Get the component parameters
     *
     * @param	string	The component option
     * @return	object	The parameters object
     */
    public function getParams($option = null)
    {
        static $params = array();
        $hash = '__default';

        if(!empty($option)) {
            $hash = $option;
        }

        if (!isset($params[$hash]))
        {
            // Get component parameters
            if (!$option) {
                $option = $this->getRequest()->getQuery()->get('option', 'cmd');
            }

            $params[$hash] = $this->getService('application.components')->getComponent(substr( $option, 4))->params;

            // Get menu parameters
            $page = $this->getService('application.pages')->getActive();

            $title  = htmlspecialchars_decode($this->getCfg('sitename' ));

            // Lets cascade the parameters if we have menu item parameters
            if (is_object($page))
            {
                $params[$hash]->merge(new JParameter((string) $page->params));
                $title = $page->title;

            }

            $params[$hash]->def( 'page_title'      , $title );
            $params[$hash]->def( 'page_description', '' );
        }

        return $params[$hash];
    }

    /**
     * Gets a configuration value.
     *
     * @param	string	$name    The name of the value to get.
     * @param	mixed	$default The default value
     * @return	mixed	The user state.
     */
    public function getCfg( $name, $default = null )
    {
        return JFactory::getConfig()->getValue('config.' . $name, $default);
    }

    /**
     * Gets the name of site
     *
     * @return	string
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * Get the theme
     *
     * @return string The theme name
     */
    public function getTheme()
    {
        return $this->getCfg('theme');
    }

    /**
     * Enqueue a system message.
     *
     * @param	string 	$msg 	The message to enqueue.
     * @param	string	$type	The message type.
     */
    function enqueueMessage( $msg, $type = 'message' )
    {
        // For empty queue, if messages exists in the session, enqueue them first
        if (!count($this->_message_queue))
        {
            $session = $this->getUser()->getSession();
            $session_queue = $this->getUser()->get('application.queue');

            if (count($session_queue))
            {
                $this->_message_queue = $session_queue;
                $this->getUser()->remove('application.queue');
            }
        }

        // Enqueue the message
        $this->_message_queue[] = array('message' => $msg, 'type' => strtolower($type));
    }

    /**
     * Get the system message queue.
     *
     * @return	The system message queue.
     */
    function getMessageQueue()
    {
        // For empty queue, if messages exists in the session, enqueue them
        if (!count($this->_message_queue))
        {
            $session_queue = $this->getUser()->get('application.queue');

            if (count($session_queue))
            {
                $this->_message_queue = $session_queue;
                $this->getUser()->remove('application.queue');
            }
        }

        return $this->_message_queue;
    }

    /**
     * Find the site name
     *
     * This function tries to get the site name based on the information present in the request. If no site can be found
     * it will return 'default'.
     *
     * @return string   The site name
     */
    protected function _findSite()
    {
        // Check URL host
        $uri  = clone(JURI::getInstance());

        $host = $uri->getHost();
        if(!$this->getService('com://admin/sites.model.sites')->getRowset()->find($host))
        {
            // Check folder
            $base = $this->getRequest()->getBaseUrl()->getPath();
            $path = trim(str_replace($base, '', $uri->getPath()), '/');
            if(!empty($path)) {
                $site = array_shift(explode('/', $path));
            } else {
                $site = 'default';
            }

            //Check if the site can be found, otherwise use 'default'
            if(!$this->getService('com://admin/sites.model.sites')->getRowset()->find($site)) {
                $site = 'default';
            }

        } else $site = $host;

        return $site;
    }
}
