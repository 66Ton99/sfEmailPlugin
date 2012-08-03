<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@require 'Swift/Transport.php';
//@require 'Swift/Transport/MailInvoker.php';
//@require 'Swift/Mime/Message.php';
//@require 'Swift/Events/EventListener.php';
require_once dirname(__FILE__) . '/Functions.php';

/**
 * Sends Messages using the files.
 * 
 * @package    Swift
 * @subpackage Transport
 * @author     Chris Corbyn
 * @author     Ton Sharp <forma@66ton99.org.ua>
 */
class sfEmail_Transport_FileTransport implements Swift_Transport
{

  /**
   * Addtional parameters to pass to file
   *
   * @var string
   */
  private $_extraParams = '-f%s';
  
  /**
   * The event dispatcher from the plugin API
   *
   * @var Swift_Events_EventDispatcher
   */
  private $_eventDispatcher;
  
  /**
   * Not in use
   *
   * @var An invoker that calls the mail() function
   */
  private $_invoker;
  
  /**
   * Create a new MailTransport with the $log.
   * 
   * @param Swift_Transport_Log $log
   */
  public function __construct(Swift_Transport_MailInvoker $invoker,
    Swift_Events_EventDispatcher $eventDispatcher)
  {
    $this->_invoker = $invoker;
    $this->_eventDispatcher = $eventDispatcher;
  }
  
  /**
   * Not used.
   */
  public function isStarted()
  {
    return false;
  }
  
  /**
   * Not used.
   */
  public function start()
  {
  }
  
  /**
   * Not used.
   */
  public function stop()
  {
  }
  
  /**
   * Set the additional parameters used on the files.
   * This string is formatted for sprintf() where %s is the sender address.
   * 
   * @param string $params
   */
  public function setExtraParams($params)
  {
    $this->_extraParams = $params;
    return $this;
  }
  
  /**
   * Get the additional parameters used on the files.
   * 
   * This string is formatted for sprintf() where %s is the sender address.
   * 
   * @return string
   */
  public function getExtraParams()
  {
    return $this->_extraParams;
  }
  
  /**
   * Send the given Message.
   * 
   * Recipient/sender data will be retreived from the Message API.
   * The return value is the number of recipients who were accepted for delivery.
   * 
   * @param Swift_Mime_Message $message
   * @param string[] &$failedRecipients to collect failures by-reference
   * 
   * @return int
   */
  public function send(Swift_Mime_Message $message, &$failedRecipients = null)
  {
    $failedRecipients = (array) $failedRecipients;
    
    if ($evt = $this->_eventDispatcher->createSendEvent($this, $message))
    {
      $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
      if ($evt->bubbleCancelled())
      {
        return 0;
      }
    }
    
    $count = (
      count((array) $message->getTo())
      + count((array) $message->getCc())
      + count((array) $message->getBcc())
      );
    
    $toHeader = $message->getHeaders()->get('To');
    $subjectHeader = $message->getHeaders()->get('Subject');
    
    $to = $toHeader->getFieldBody();
    $subject = $subjectHeader->getFieldBody();
    
    $reversePath = $this->_getReversePath($message);
    
    //Remove headers that would otherwise be duplicated
    $message->getHeaders()->remove('Subject');
    
    $messageStr = $message->toString();
    
    $message->getHeaders()->set($toHeader);
    $message->getHeaders()->set($subjectHeader);
    
    //Separate headers from body
    if (false !== $endHeaders = strpos($messageStr, "\r\n\r\n"))
    {
      $headers = substr($messageStr, 0, $endHeaders) . "\r\n"; //Keep last EOL
      $body = substr($messageStr, $endHeaders + 4);
    }
    else
    {
      $headers = $messageStr . "\r\n";
      $body = '';
    }
    
    unset($messageStr);
    
    if ("\r\n" != PHP_EOL) //Non-windows (not using SMTP)
    {
      $headers = str_replace("\r\n", PHP_EOL, $headers);
      $body = str_replace("\r\n", PHP_EOL, $body);
    }
    else //Windows, using SMTP
    {
      $headers = str_replace("\r\n.", "\r\n..", $headers);
      $body = str_replace("\r\n.", "\r\n..", $body);
    }
    
    emailPluginWriteMail($body, $subject, $headers);
//    if ($this->_invoker->mail($to, $subject, $body, $headers, sprintf($this->_extraParams, $reversePath))

    return $count;
  }
  
  /**
   * Register a plugin.
   * 
   * @param Swift_Events_EventListener $plugin
   */
  public function registerPlugin(Swift_Events_EventListener $plugin)
  {
    $this->_eventDispatcher->bindEventListener($plugin);
  }
  
  // -- Private methods
  
  /**
   * Determine the best-use reverse path for this message
   *
   * @param Swift_Mime_Message $message
   *
   * @return string
   */
  private function _getReversePath(Swift_Mime_Message $message)
  {
    $return = $message->getReturnPath();
    $sender = $message->getSender();
    $from = $message->getFrom();
    $path = null;
    if (!empty($return))
    {
      $path = $return;
    }
    elseif (!empty($sender))
    {
      $keys = array_keys($sender);
      $path = array_shift($keys);
    }
    elseif (!empty($from))
    {
      $keys = array_keys($from);
      $path = array_shift($keys);
    }
    return $path;
  }
  
}


/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@require 'Swift/Transport/MailTransport.php';
//@require 'Swift/DependencyContainer.php';

/**
 * Sends Messages using the files.
 * 
 * @package    Swift
 * @subpackage Transport
 * @author     Chris Corbyn
 * @author     Ton Sharp <forma@66ton99.org.ua>
 */
class sfEmail_FileTransport extends sfEmail_Transport_FileTransport
{
  
  /**
   * Create a new MailTransport, optionally specifying $extraParams.
   * 
   * @param string $extraParams
   * 
   * @return void
   */
  public function __construct($extraParams = '-f%s')
  {
    call_user_func_array(
      array($this, 'sfEmail_Transport_FileTransport::__construct'),
      Swift_DependencyContainer::getInstance()
        ->createDependenciesFor('transport.mail')
      );
    
    $this->setExtraParams($extraParams);
  }
  
  /**
   * Create a new MailTransport instance.
   * 
   * @param string $extraParams To be passed to file
   * 
   * @return Swift_MailTransport
   */
  public static function newInstance($extraParams = '-f%s')
  {
    return new self($extraParams);
  }
  
}
