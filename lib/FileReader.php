<?php

/**
 * sfEmailPlugin file reader
 * @package Swift_Connection
 * @author Alex Emelyanov <alex.emelyanov.ua@gmail.com>
 */
class sfEmail_FileReader
{

  /**
   * @var string
   */
  private $path;

  /**
   * 
   */
  public function __construct()
  {
    $this->path = sfConfig::get('sf_emailPlugin_path');
  }

  /**
   * Just clean folder
   */
  public function clean()
  {
    if ($files = $this->getList()) {
      foreach ($files as $file) {
        @unlink($this->path.'/'.$file);
      }
    }
  }

  /**
   * @return array
   */
  public function getList()
  {
    if ($files = sfFinder::type('file')
        ->name("*.eml")
        ->relative()
        ->prune('om')
        ->ignore_version_control()
        ->in($this->path)
    ) {
      sort($files);
    }
    return $files;
  }

  public function getEmail($file)
  {
    $file = $this->path . '/' . $file;
    if (!is_readable($file)) {
      throw new sfEmailException("File '{$file}' does not exist or no access");
    }
    $email = new Zend_Mail_Message(array('file' => $file));
    return $email;
  }

  /**
   * @param string $file
   */
  public function readEmail($file)
  {
    return $this->getEmail($file)->getContent();
  }
}
