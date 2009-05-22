<?php

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->setPlugins(array('sfZ3950Plugin'));
    $this->setPluginPath('sfZ3950Plugin', dirname(__FILE__).'/../../../..');
  }
}
