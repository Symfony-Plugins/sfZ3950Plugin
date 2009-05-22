<?php

/**
 * sfZ3950Plugin configuration.
 * 
 * @package     sfZ3950Plugin
 * @subpackage  config
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version     SVN: $Id$
 */
class sfZ3950PluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if(!function_exists('yaz_connect'))
    {
      $error = 'The driver YAZ was not loaded';
      throw new sfZ3950Exception($error);
    }
    
    if (sfConfig::get('sf_web_debug'))
    {
      require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelZ3950.class.php';
      $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelZ3950', 'listenToLoadPanelEvent'));
    }
  }
}
