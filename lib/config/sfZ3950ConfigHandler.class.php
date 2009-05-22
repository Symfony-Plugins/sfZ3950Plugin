<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_ConfigHandler
 *
 * Parses the z3950.yml and produces a config php file
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950ConfigHandler extends sfYamlConfigHandler
{
  public function execute($configFiles)
  {
    // parse the yaml
    $config = self::getConfiguration($configFiles);
    $environment = sfConfig::get('sf_environment');
    
    $data = array();
    foreach($config AS $env => $connection)
    {
      if(($env == 'all') || ($env == $environment))
      {
        foreach($config[$env] AS $name => $params)
        {
          foreach($params['indexes'] AS $key => $value)
          {
            $data[$key] = $value;
          }
        }
      }
    }

    $_array = 'array(';
    foreach($data AS $key => $value)
    {
      $_array.= "'$key' => '$value', ";
    }
    $_array .= ')';
    
    $data = array();
    $data[] = sprintf("sfConfig::add(array('sfZ3950_%s_indexes' => %s));", $name, $_array);
    
    return sprintf("<?php\n".
                   "// auto-generated by sfZ3950ConfigHandler\n".
                   "// date: %s\n%s\n", date('Y/m/d H:i:s'), implode("\n", $data));
  }

  /**
   * @see sfConfigHandler
   */
  static public function getConfiguration(array $configFiles)
  {
    return self::parseYamls($configFiles);
  }
  
}