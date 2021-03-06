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
  set_include_path(__DIR__ . '/vendor' . PATH_SEPARATOR . get_include_path());

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
  $dir = sfConfig::get('sf_emailPlugin_path');
  if (!is_dir($dir)) {
    $path = '';
    foreach (explode(DIRECTORY_SEPARATOR, substr($dir, 1)) as $pathDir) {
      $path .= DIRECTORY_SEPARATOR . $pathDir;
      if (!is_dir($path)) {
        @mkdir($path);
        @chmod($path, 0777);
      }
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

  @chmod($filename, 0777);
}
