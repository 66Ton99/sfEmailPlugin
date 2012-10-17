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
    $this->sfEmail_FileReader = new sfEmail_FileReader();
  }

  /**
   * List of emails
   *
   * @return void
   */
  public function executeIndex()
  {
    $this->files = $this->sfEmail_FileReader->getList();
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
    $this->logMessage($this->filename, 'debug');
    try {
      $this->message = $this->sfEmail_FileReader->getEmail($this->filename);
    } catch (Zend_Mail_Exception $e) {
      return false;
    }

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
