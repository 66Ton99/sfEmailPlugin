<?php

require_once dirname(__FILE__) . '/../lib/Functions.php';

/** 
 * Configuation Symfony 1.2 - 1.4
 *
 * @package    sfEmailPlugin
 * @subpackage Config
 * 
 * @author     Ton Sharp <forma@66Ton99.org.ua>
 */
class sfEmailPluginConfiguration extends sfPluginConfiguration
{
  
  /**
   * Initialize
   *
   * @return void
   */
  public function initialize()
  {
    emailPluginInitialisation($this->configuration);
  }
}