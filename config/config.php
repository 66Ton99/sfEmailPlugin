<?php
/**
 * Configuration Symfony 1.0 - 1.1
 *
 * @package    sfEmailPlugin
 * @subpackage Config
 * 
 * @author     Ton Sharp <forma@66Ton99.org.ua>
 */

require_once dirname(__FILE__) . '/../lib/Functions.php';


if (defined('SYMFONY_VERSION') && '1.1.' == substr(SYMFONY_VERSION, 0, 4)) {
  emailPluginInitialisation($this);
} else {
  $configFiles = sfLoader::getConfigPaths(EMAIL_PLUGIN_CONFIG);
  foreach ($configFiles as $file) {
    $config = sfYaml::load($file);
    foreach (array('all', sfConfig::get('sf_evirement')) as $section) {
      if (empty($config[$section])) continue;
      emailPluginSetConfigs($config[$section]);
    }
  }
}

