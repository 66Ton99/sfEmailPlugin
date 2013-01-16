<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
      Swift_DependencyContainer::getInstance()->createDependenciesFor('transport.mail')
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
