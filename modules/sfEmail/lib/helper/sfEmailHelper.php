<?php
/**
 * sfEmail helper.
 *
 * @package    sfEmailPlugin
 * @subpackage Helper
 * @author     Voznyak Nazar <voznyaknazar@gmail.com>
 * @website    http://narkozateam.com
 * @author     Ton Sharp <forma@66ton99.org.ua>
 */

/**
 * 
 *
 * @param string $path
 * @param string $name
 *
 * @return string
 */
function link_to_file($path, $name = null, $arguments = array())
{
  return link_to(($name === null ? $path : $name),
  							 'sfEmail/showFile?filename=' . rawurlencode(str_replace(array('\\', '.'),
                                                                         array('/', '%%'),
                                                                         $path)), 
                 $arguments);
}