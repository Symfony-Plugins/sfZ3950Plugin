<?php

/*
 * This file is part of the sfZ3950Plugin.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelZ3950 adds a panel to the web debug toolbar with Z3950 information.
 *
 * @package    sfZ3950Plugin
 * @subpackage debug
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfWebDebugPanelZ3950 extends sfWebDebugPanel
{
  public function __construct(sfWebDebug $webDebug)
  {
    parent::__construct($webDebug);
  }
  
  public function getTitle()
  {
    if ($sqlLogs = $this->getZ3950Logs())
    {
      return '<img src="/sfZ3950Plugin/images/magnifier.png" alt="Z3950 queries" /> '.count($sqlLogs);
    }
  }

  public function getPanelTitle()
  {
    return 'Z3950 queries';
  }
  
  public function getPanelContent()
  {
    $logs = array();
    
    foreach ($this->getZ3950Logs() as $log)
    {
      $logs[] = htmlspecialchars($log, ENT_QUOTES, sfConfig::get('sf_charset'));
    }
    
    return '
      <div id="sfWebDebugDatabaseLogs">
      <ol><li>'.implode("</li>\n<li>", $logs).'</li></ol>
      </div>
    ';
    
  }


  static public function listenToLoadPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('Z3950', new self($event->getSubject()));
  }
  
  
  protected function getZ3950Logs()
  {
    $logs = array();
    $bindings = array();
    $i = 0;
    foreach ($this->webDebug->getLogger()->getLogs() as $log)
    {
      if ('sfZ3950Logger' != $log['type'])
      {
        continue;
      }
      
      $logs[$i++] = $log['message'];
    }
    
    return $logs;
  }
}