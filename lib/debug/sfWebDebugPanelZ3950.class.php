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
    $this->webDebug->getEventDispatcher()->connect('debug.web.filter_logs', array($this, 'filterLogs'));
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
    return '
      <div id="sfWebDebugDatabaseLogs">
      <ol><li>'.implode("</li>\n<li>", $this->getZ3950Logs()).'</li></ol>
      </div>
    ';
    
  }


  static public function listenToLoadPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('Z3950', new self($event->getSubject()));
  }
  
  
  public function filterLogs(sfEvent $event, $newZ3950logs)
  {
    $newLogs = array();
    foreach ($newZ3950logs as $newZ3950log)
    {
      if ('sfZ3950Logger' != $newZ3950log['type'])
      {
        $newLogs[] = $newZ3950log;
      }
    }

    return $newLogs;
  }
  
  protected function getZ3950Logs()
  {
    $logs = array();
    $i = 0;
    foreach ($this->webDebug->getLogger()->getLogs() as $log)
    {
      if ('sfZ3950Logger' != $log['type'])
      {
        continue;
      }
      
      $logs[$i++] = self::formatZ3950($log['message']);
    }
    
    return $logs;
  }
  
  
  static protected function formatZ3950($mes)
  {
    $color_a = '#CC0033';
    $color_b = '#0000FF';
    $color_c = '#339900';
    $color_d = '#9933CC';
    
    $indexes = sfConfig::get('sfZ3950_'.preg_replace('/^(.*?):.*/', '$1', $mes).'_indexes');
    $indexes = array_flip($indexes);
    
    $mes = "<b>$mes</b>";
    
    foreach($indexes AS $key => $value)
    {
      $mes = str_replace($key, "<span style=\"color: $color_c;\"><b>$key</b></span>", $mes);
    }
    
    $mes = str_replace('@and', "<span style=\"color: $color_a;\"><b>@and</b></span>", $mes);
    $mes = str_replace('@or', "<span style=\"color: $color_a;\"><b>@or</b></span>", $mes);
    
    $mes = preg_replace('/(@attr)/', "<span style=\"color: $color_b\"><b>$1</b></span>", $mes);
    $mes = preg_replace('/(\(limit.*\))/', "<span style=\"color: $color_d\"><b>$1</b></span>", $mes);
    
    return $mes;
  }
}