<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2008 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Manager
 *
 * Provides connectivity for Z3950.
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Manager
{
  public static function connection($manager, $dsn, $options, $name)
  {
    return sfZ3950_Manager::getInstance()->openConnection($manager, $dsn, $options, $name);
  }
  
  
  public static function getInstance()
  {
      static $instance;
      if ( ! isset($instance)) {
          $instance = new self();
      }
      return $instance;
  }
  
  
  public function openConnection($manager, $dsn, $options, $name)
  {
    $this->manager = $manager;
    $params = self::ParseDSN($dsn);
    $options = array('phptype', 'hostspec', 'database', 'username', 'password', 'port');
    $this->setZ3950Parameter($params, $options);
    
    if($params = $manager->getParameter('options'))
    {
      $options = array('protocol', 'group', 'cookie', 'proxy', 'persistent', 'piggyback', 'charset', 'preferredMessageSize', 'maximumRecordSize');
      $this->setZ3950Parameter($params, $options);
    }
    
    try
    {
      $hostspec = $this->manager->getParameter('hostspec');
      $database = $this->manager->getParameter('database');
      $port = $this->manager->getParameter('port', 210);
    
      $zurl = "$hostspec:$port/$database";
      if(!$protocol = $this->manager->getParameter('protocol'))
      {
        $connection = yaz_connect($zurl);
      }
      else
      {
        $conf = array(
            'protocol'   => $this->manager->getParameter('protocol', 2),
            'user'       => $this->manager->getParameter('username'),
            'password'   => $this->manager->getParameter('password'),
            'group'      => $this->manager->getParameter('group'),
            'cookie'     => $this->manager->getParameter('cookie'),
            'proxy'      => $this->manager->getParameter('proxy'),
            'persistent' => $this->manager->getParameter('persistent', true),
            'piggyback'  => $this->manager->getParameter('piggyback', true),
            'charset'    => $this->manager->getParameter('charset'),
            'preferredMessageSize' => $this->manager->getParameter('preferredMessageSize'),
            'maximumRecordSize' => $this->manager->getParameter('maximumRecordSize'),
          );
        $options = $this->parseOptionsWithProtocol($protocol, $conf);
        $connection = yaz_connect($zurl, $options);
      }
    }
    catch (SQLException $e)
    {
      throw new sfZ3950Database($e->toString());
    }
    
    return $connection;
  }
  
  
  private function setZ3950Parameter($params, $options)
  {
    foreach ($options as $option)
    {
      if (!$this->manager->getParameter($option) && isset($params[$option]))
      {
        $this->manager->setParameter($option, $params[$option]);
      }
    }
  }
  
  
  private function parseOptionsWithProtocol($protocol, $options)
  {
    $parsed = ($protocol == 2) ? '' : array();
    
    foreach($options AS $key => $value)
    {
      if($key == 'persistent' || $key == 'piggyback')
      {
        $value = (!$value) ? '0' : '1';
      }
      
      if($value != '')
      {
        if($key != 'protocol')
        {
          if($protocol == 2)
          {
            $parsed .= "$key=$value,";
          }
          else
          {
            $parsed[$key] = $value;
          }
        }
      }
    }
    
    if($protocol == 2) { $parsed = substr($parsed, 0, -1); }
    return $parsed;
  }
  
  
  private static function parseDSN($dsn)
  {
      if (is_array($dsn)) {
          return $dsn;
      }

      $parsed = array(
          'phptype'  => null,
          'username' => null,
          'password' => null,
          'hostspec' => null,
          'port'     => null,
          'database' => null
      );

      $info = parse_url($dsn);
      
      if (count($info) === 1) { // if there's only one element in result, then it must be the phptype
          $parsed['phptype'] = array_pop($info);
          return $parsed;
      }

      // some values can be copied directly
      $parsed['phptype'] = @$info['scheme'];
      $parsed['username'] = @$info['user'];
      $parsed['password'] = @$info['pass'];
      $parsed['port'] = @$info['port'];

      $host = @$info['host'];
      $parsed['hostspec'] = $host;

      if (isset($info['path'])) {
          $parsed['database'] = substr($info['path'], 1); // remove first char, which is '/'
      }

      return $parsed;
  }
}
