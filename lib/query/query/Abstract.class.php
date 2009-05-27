<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Query_Abstract
 *
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
abstract class sfZ3950_Query_Abstract
{
  private
    $_conn,
    $_hydration,
    $_where;
  
  public
    $_parts;
  
  public function __construct($connection = null, $dbname = null, $parts = array())
  {
    if($connection === null && $dbname != null)
    {
      $connection = sfContext::getInstance()->getDatabaseConnection($dbname);
    }
    $this->_conn = $connection;
    $this->_parts = $parts;
    
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $this->logger = new sfZ3950Logger();
    }
  }

  public function from($dbname)
  {
    $this->_parts['from'] = $dbname;
    return new sfZ3950_Query_From($this->_conn, $dbname, $this->_parts);
  }
  
  public function where($query)
  {
    $this->_parts['where'] = $query;
    $this->_parts['conf'] = $this->loadConfiguration($this->_conn, $this->_parts['from']);
    $this->_parts['rpn'] = $this->whereParse($this->_conn, $query);
    return new sfZ3950_Query_Where($this->_conn, $query, $this->_parts);
  }

  public function orderBy($order)
  {
    $this->_parts['order'] = $order;
    return new sfZ3950_Query_OrderBy($this->_conn, $order, $this->_parts);
  }
  
  public function limit($start, $end)
  {
    /* La position du tableau commence à 1 */
    $this->_parts['limit'] = array('start' => $start + 1, 'end' => $end);
    return new sfZ3950_Query_Limit($this->_conn, $this->_parts);
  }

  public function execute($hydrationMode = null, $syntaxMode = 'usmarc')
  {
    $this->_parts['syntax'] = $syntaxMode;
    $this->_parts = $this->find($this->_conn, $this->_parts);
    $hydrationType = isset($this->_parts['limit']) ?
                  array('type' => 'limit',
                        'start' => $this->_parts['limit']['start'],
                        'end' => $this->_parts['limit']['end']):
                  array('type' => 'all');
    return new sfZ3950_Hydration($this->_conn, $this->_parts, $hydrationMode, $hydrationType);
  }

  public function fetchOne($hydrationMode = null, $syntaxMode = 'usmarc')
  {
    $this->_parts['syntax'] = $syntaxMode;
    $this->_parts = $this->find($this->_conn, $this->_parts);
    return new sfZ3950_Hydration($this->_conn, $this->_parts, $hydrationMode, array('type' =>'one'));
  }
  
  private function find($conn, $parts)
  {
    yaz_syntax($conn, $parts['syntax']);
    yaz_search($conn, "rpn", $this->_parts['rpn']);
    yaz_wait();
    
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $mes = $parts['from'].': '.$parts['rpn'];
      if(isset($this->_parts['limit']))
      {
        $mes .= ' (limit '.$this->_parts['limit']['start'].','.$this->_parts['limit']['end'].')';
      }
      $this->logger->log($mes);
    }
    
    $parts['error_no'] = yaz_errno($conn);
    $parts['error'] = yaz_error($conn);
    return $parts;
  }
  
  private function loadConfiguration($conn, $from)
  {
    if($file = sfProjectConfiguration::getActive()->getConfigCache()->checkConfig('config/z3950.yml'))
    {
      require($file);
    }
    else
    {
      throw new sfZ3950Exception('The file z3950.yml is not present in your config folder');
    }
    
    $indexes = sfConfig::get('sfZ3950_'.$from.'_indexes');
    yaz_ccl_conf($conn, $indexes);
    return $indexes;
  }
  
  private function whereParse($conn, $where)
  {
    if(!yaz_ccl_parse($conn, $where, $result))
    {
      throw new sfZ3950Exception(sprintf('Parsing error: %s', $result['errorstring']));
    }
    return trim($result["rpn"]);
  }
  
  public function getZ3950Request()
  {
    return $this->_parts;
  }
}
