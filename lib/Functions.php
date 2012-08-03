<?php
/** 
 * Functions
 *
 * @package    sfEmailPlugin
 * @subpackage Config
 * @author     Ton Sharp <forma@66Ton99.org.ua>
 */

define('EMAIL_PLUGIN_CONFIG', 'config/emailPlugin.yml');

/**
 * Initialisated conguration
 *
 * @param sfApplicationConfiguration $configuration
 *
 * @return void
 */
function emailPluginSetConfigs($configs)
{
  foreach ($configs as $name => $value) {
    sfConfig::set("sf_emailPlugin_{$name}", $value);
  }
}

/**
 * Initialisated conguration
 *
 * @param unknow $configuration
 *
 * @return void
 */
function emailPluginInitialisation($configuration)
{
  $configFiles = $configuration->getConfigPaths(EMAIL_PLUGIN_CONFIG);
  $configs = sfDefineEnvironmentConfigHandler::getConfiguration($configFiles);
  emailPluginSetConfigs($configs);
}

/**
 * Write email to the file
 *
 * @param string $body
 * @param string $subject
 * @param string $header
 * @throws sfException
 *
 * @return
 */
function emailPluginWriteMail($body, $subject, $header)
{
  $dir = sfConfig::get('sf_root_dir') . sfConfig::get('sf_emailPlugin_path');
  if (!is_dir($dir)) {
    $separator = substr(sfConfig::get('sf_emailPlugin_path'), 0, 1);
    $path = sfConfig::get('sf_root_dir');
    foreach (explode($separator, substr(sfConfig::get('sf_emailPlugin_path'), 1)) as $pathDir) {
      $path .= DIRECTORY_SEPARATOR . $pathDir;
      @mkdir($path);
      @chmod($path, 0777);
    }
  }

  $base = $dir . DIRECTORY_SEPARATOR . date('Y-m-d_H:i:s');
  $i = 1;
  while(true) {
    $filename = "{$base}-{$i}.eml";
    if (is_file($filename)) {
      $i++;
    } else {
      break;
    }
  }

  $fout = fopen($filename, "w");
  if (!$fout) {
    throw new sfException("Cant open file {$filename} for writing");
  }

  fwrite($fout, $header);
  fwrite($fout, "Subject: " . $subject);
  fwrite($fout, "\n\n");
  fwrite($fout, $body);

  fclose ($fout);
}