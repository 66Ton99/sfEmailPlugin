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
    $this->setLayout(false);
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
    if(!$this->filename = str_replace('%%', '.', $this->getRequestParameter('filename'))) {
      return false;
    }
    $file = realpath(sfConfig::get('sf_root_dir') . sfConfig::get('sf_emailPlugin_path') . '/' . $this->filename);
    $this->logMessage($file, 'debug');
    if(!(0 === strpos($file, sfConfig::get('sf_root_dir')) && file_exists($file))) {
      return false;
    }

    $this->message = new Zend_Mail_Message(array('file' => $file));

    $this->options = array(
    	'contentType' => 'Content-type: ',
    	'from' => 'From: ',
    	'to' => 'To: ',
    	'subject' => 'Subject: ',
    );
    foreach ($this->options as $key => $val) {
      $this->$key = $this->message->$key;
    }
    $this->subject = quoted_printable_decode($this->subject);
    $this->messages= array();
    $this->types = array();
    for ($i = 1; $i <= $this->message->countParts(); $i++) {
      $part = $this->message->getPart($i);
      $clearType = strtok($part->contentType, ';');
      if ($clearType == $this->getRequestParameter('type', 'text/plain') ) {
        $this->contentType = $part->contentType;
        $this->messages[$part->contentType] = quoted_printable_decode($part->getContent());
      } else {
        $this->types[] = $clearType;
      }
    }

    if ('text/html' == $this->getRequestParameter('type')) {
      $this->content = array_pop($this->messages);
      $this->setTemplate('content');
    }

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
