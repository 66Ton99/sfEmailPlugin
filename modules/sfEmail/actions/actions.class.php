<?php

/**
 * sfEmail actions.
 *
 * @package    sfEmailPlugin
 * @subpackage Module
 * @author     Voznyak Nazar <voznyaknazar@gmail.com>
 * @website    http://narkozateam.com
 * @author     Ton Sharp <forma@66ton99.org.ua>
 */
class sfEmailActions extends sfActions
{

  /**
   * (non-PHPdoc)
   * @see sfAction::preExecute()
   */
  public function preExecute()
  {
//    $this->setLayout(false);
  }
  
  /**
   * List of emails
   * 
   * @return void
   */
  public function executeIndex()
  {
    $this->files = array();
    $this->path = sfConfig::get('sf_root_dir') . sfConfig::get('sf_emailPlugin_path');
    if ($files = sfFinder::type('file')
      ->name("*.eml")
      ->relative()
      ->prune('om')
      ->ignore_version_control()
      ->in($this->path))
    {
      sort($files);
      $this->files = $files;
    }    
  }

  /**
   * 
   *
   * @return bool
   */
  private function retrieveFile()
  {
    if(!$filename = str_replace('%%', '.', $this->getRequestParameter('filename'))) {
      return false;
    }    
    $file = realpath(sfConfig::get('sf_root_dir') . sfConfig::get('sf_emailPlugin_path') . '/' . $filename);
    $this->logMessage($file, 'debug');
    if(!(0 === strpos($file, sfConfig::get('sf_root_dir')) && file_exists($file))) {
      return false;
    }
    $this->filename = $filename;
    $file = file_get_contents($file);
    
    if (empty($file)) return false;
    
    $line = '';
    $this->options = array(
    	'type' => 'Content-type: ',
    	'from' => 'From: ',
    	'to' => 'To: ',
    	'subject' => 'Subject: ',
    );
    $i = 0;
    $ins = 0;
    foreach (explode("\n", $file) as $line) {
      $i += strlen($line)+1;
      foreach ($this->options as $key => $val) {
        if (strtolower($val) == strtolower(substr($line, 0, strlen($val)))) {
          $this->$key = substr($line, strlen($val));
        }
      }
      if ('' == $line) {
        $ins++;
        if (1 == $ins) {
          $file = substr($file, $i);
          break;
        }
        continue;
      }
      $ins = 0;
    }
    $type = explode(';', @$this->type);
    switch (trim(@$type[0])) {
      case 'text/html':
        $this->output = quoted_printable_decode($file);
        if (!empty($this->subject)) {
          $this->subject = quoted_printable_decode($this->subject);
        }
        break;
      
      default:
        $this->output = nl2br($file);
        break;
    }
    $this->output = str_replace(array('<html>', '</html>', '<body', '</body>', '<head>', '</head>'), 
                                array('', '', '<div', '</div>', '', ''),
                                $this->output);
    $this->output = base64_encode($this->output);
//                                $this->output = 'asdgdsfgsdfg';
//                                var_dump($this->output);
    return true;
  }

  /**
   * Display email
   *
   * @return void
   */
  public function executeShowFile()
  {
    $this->forward404Unless($this->retrieveFile());
  }

}
