<?php

require_once dirname(__FILE__) . '/Functions.php';

/**
 * Wrapper for sfMail
 *
 * @package    sfEmailPlugin
 * @subpackage config 
 * @author     Ton Sharp <forma@66Ton99.org.ua>
 */
class sfEmail extends sfMail {

  /**
   * (non-PHPdoc)
   * @see sfMail::send()
   */
  public function send()
  {
    if ('prod' == sfConfig::get('sf_environment')) {
      parent::send();
    } else { 
      emailPluginWriteMail($this->getBody(), $this->getSubject(), $this->getRawHeader());
    }
  }
}