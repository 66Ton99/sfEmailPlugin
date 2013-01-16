<?php 

/**
 * Memory Swift mailer transport for unit tests
 * 
 * @package    sfEmailPlugin
 * @subpackage Transport
 * @author     Ton Sharp <forma@66ton99.org.ua>
 */
class sfEmail_Transport_MemTransport implements Swift_Transport
{

  /**
   * Messages storage
   *
   * @var array
   */
  private static $_testMessArray = array();

  /**
   * Status
   *
   * @var bool
   */
  private static $_isEnabled = true;
  
  /**
   * Test if this Transport mechanism has started.
   * 
   * @return boolean
   */
  public function isStarted()
  {
    return true;
  }
  
  /**
   * @param bool $var
   */
  public static function setEnable($var)
  {
    self::$_isEnabled = (bool)$var;
  }
  
  /**
   * Start this Transport mechanism.
   */
  public function start() {}
  
  /**
   * Stop this Transport mechanism.
   */
  public function stop() {}
  
  /**
   * Send the given Message.
   * 
   * Recipient/sender data will be retreived from the Message API.
   * The return value is the number of recipients who were accepted for delivery.
   * 
   * @param Swift_Mime_Message $message
   * @param string[] &$failedRecipients to collect failures by-reference
   * @return int
   */
  public function send(Swift_Mime_Message $message, &$failedRecipients = null)
  {
    if (self::$_isEnabled) {
      self::$_testMessArray[] = array('message' => $message, 'failedRecipients' => ($failedRecipients));
    }
  }
  
  /**
   * Register a plugin in the Transport.
   * 
   * @param Swift_Events_EventListener $plugin
   */
  public function registerPlugin(Swift_Events_EventListener $plugin) {}

  /**
   * @return array
   */
  public static function getMess()
  {
    return self::$_testMessArray;
  }

  /**
   * Clear all messages
   */
  public static function clearMess()
  {
    self::$_testMessArray = array();
  }
  
  /**
   * Default checking 
   * 
   * @return bool|string - return true if OK or error message
   */
  public static function checkMails()
  {
    foreach (self::getMess() as $mail) {
      $cc = $mail['message']->getCc();
      if (!empty($cc)) return 'CC not empty';
      $bcc = $mail['message']->getBcc();
      if (!empty($bcc)) return 'BCC not empty';
      $recipients = $mail['message']->getTo();
      
      if (!is_array($recipients)) {
        $recipients = explode(',', $recipients);
      }
      
      
      
      if (1 < count($recipients)) return 'Worong number of recipients';
      
      /*foreach ($recipients as $adress) {
        if (false !== strpos($adress, ',') ||
            false !== strpos($adress, ';') ||
            false !== strpos($adress, ' ')) return "Wrong chars in email adress {$adress}";
      }*/
            
      if (!trim($mail['message']->getBody())) return 'Empty body';
      
    }
    return true;
  }
}
