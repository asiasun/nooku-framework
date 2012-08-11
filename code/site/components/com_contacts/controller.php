<?php
/**
 * @version		$Id: controller.php 14974 2010-02-21 14:32:22Z ian $
 * @package		Joomla
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * Contact Component Controller
 *
 * @static
 * @package		Joomla
 * @subpackage	Contact
 * @since 1.5
 */
class ContactController extends JController
{
	/**
	 * Method to send an email to a contact
	 *
	 * @static
	 * @since 1.0
	 */
	function submit()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize some variables
		$db			= & JFactory::getDBO();
		$SiteName	= $mainframe->getCfg('sitename');

		$default	= JText::sprintf( 'MAILENQUIRY', $SiteName );
		$contactId	= JRequest::getInt( 'id',			0,			'post' );
		$name		= JRequest::getVar( 'name',			'',			'post' );
		$email		= JRequest::getVar( 'email',		'',			'post' );
		$subject	= JRequest::getVar( 'subject',		$default,	'post' );
		$body		= JRequest::getVar( 'text',			'',			'post' );
		$emailCopy	= JRequest::getInt( 'email_copy', 	0,			'post' );

		 // load the contact details
		$model		= &$this->getModel('contact');

		// query options
		$qOptions['id']	= $contactId;
		$contact		= $model->getContact( $qOptions );

		/*
		 * If there is no valid email address or message body then we throw an
		 * error and return false.
		 */
		jimport('joomla.mail.helper');
		if (!$email || !$body || (JMailHelper::isEmailAddress($email) == false))
		{
			$this->setError(JText::_('CONTACT_FORM_NC'));
			$this->display();
			return false;
		}

		// Contact plugins
		JPluginHelper::importPlugin( 'contact' );
		$dispatcher	=& JDispatcher::getInstance();

		// Input validation
		if  (!$this->_validateInputs( $contact, $email, $subject, $body ) ) {
			JError::raiseWarning( 0, $this->getError() );
			return false;
		}

		// Custom handlers
		$post		= JRequest::get( 'post' );
		$results	= $dispatcher->trigger( 'onValidateContact', array( &$contact, &$post ) );

		foreach ($results as $result)
		{
			if (JError::isError( $result )) {
				return false;
			}
		}

		// Passed Validation: Process the contact plugins to integrate with other applications
		$results	= $dispatcher->trigger( 'onSubmitContact', array( &$contact, &$post ) );

		$pparams = &$mainframe->getParams('com_contact');
		if (!$pparams->get( 'custom_reply' ))
		{
			$MailFrom 	= $mainframe->getCfg('mailfrom');
			$FromName 	= $mainframe->getCfg('fromname');

			// Prepare email body
			$prefix = JText::sprintf('ENQUIRY_TEXT', JURI::base());
			$body 	= $prefix."\n".$name.' <'.$email.'>'."\r\n\r\n".stripslashes($body);

			$mail = JFactory::getMailer();

			$mail->addRecipient( $contact->email_to );
			$mail->setSender( array( $email, $name ) );
			$mail->setSubject( $FromName.': '.$subject );
			$mail->setBody( $body );

			$sent = $mail->Send();

			/*
			 * If we are supposed to copy the admin, do so.
			 */
			// parameter check
			$params = new JParameter( $contact->params );
			$emailcopyCheck = $params->get( 'show_email_copy', 0 );

			// check whether email copy function activated
			if ( $emailCopy && $emailcopyCheck )
			{
				$copyText 		= JText::sprintf('Copy of:', $contact->name, $SiteName);
				$copyText 		.= "\r\n\r\n".$body;
				$copySubject 	= JText::_('Copy of:')." ".$subject;

				$mail = JFactory::getMailer();

				$mail->addRecipient( $email );
				$mail->setSender( array( $MailFrom, $FromName ) );
				$mail->setSubject( $copySubject );
				$mail->setBody( $copyText );

				$sent = $mail->Send();
			}
		}

		$msg = JText::_( 'Thank you for your e-mail');
		$link = JRoute::_('index.php?option=com_contact&view=contact&id='.$contact->slug.'&category='.$contact->catslug, false);
		$this->setRedirect($link, $msg);
	}

	/**
	 * Validates some inputs based on component configuration
	 *
	 * @param Object	$contact	JTable Object
	 * @param String	$email		Email address
	 * @param String	$subject	Email subject
	 * @param String	$body		Email body
	 * @return Boolean
	 * @access protected
	 * @since 1.5
	 */
	function _validateInputs( $contact, $email, $subject, $body )
	{
		global $mainframe;

		$session =& JFactory::getSession();

		// Get params and component configurations
		$params		= new JParameter($contact->params);
		$pparams	= &$mainframe->getParams('com_contact');

		// check for session cookie
		$sessionCheck 	= $pparams->get( 'validate_session', 1 );
		$sessionName	= $session->getName();
		if  ( $sessionCheck ) {
			if ( !isset($_COOKIE[$sessionName]) ) {
				$this->setError( JText::_('ALERTNOTAUTH') );
				return false;
			}
		}

		// test to ensure that only one email address is entered
		$check = explode( '@', $email );
		if ( strpos( $email, ';' ) || strpos( $email, ',' ) || strpos( $email, ' ' ) || count( $check ) > 2 ) {
			$this->setError( JText::_( 'You cannot enter more than one email address', true ) );
			return false;
		}

		return true;
	}

	/**
	 * Checks $text for values contained in the array $array, and sets error message if true...
	 *
	 * @param String	$text		Text to search against
	 * @param String	$list		semicolon (;) seperated list of banned values
	 * @return Boolean
	 * @access protected
	 * @since 1.5.4
	 */
	function _checkText($text, $list) {
		if(empty($list) || empty($text)) return true;
		$array = explode(';', $list);
		foreach ($array as $value) {
			$value = trim($value);
			if(empty($value)) continue;
			if ( JString::stristr($text, $value) !== false ) {
				return false;
			}
		}
		return true;
	}



}
