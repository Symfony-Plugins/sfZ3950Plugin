<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Hydration
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Hydration
{
  private
  $_conn,
  $_parts,
  $_hydrationMode,
  $_hydration,
  $_hydrationType;
  
  public function __construct($conn, $parts, $hydrationMode, $type = array('type' => 'all'))
  {
    $this->_hydrationMode = ($hydrationMode == null) ? 'raw': $hydrationMode;
    if(!in_array($this->_hydrationMode, sfZ3950_Record::getAllowHydrationMode()))
    {
      throw new sfZ3950QueryException(sprintf('The %s is not allowed', $hydrationMode));
    }
    else
    {
      $this->_conn = $conn;
      $this->_parts = $parts;
      $this->_hydrationType = $type;
      $this->setHydration();
    }
  }
  
  private function setHydration()
  {
    if($this->_hydrationType['type'] != 'one')
    {
      $this->_records['hits'] = yaz_hits($this->_conn);
      if($this->_hydrationType['type'] == 'limit')
      {
        $start = $this->_hydrationType['start']; /* Le résultat commence à 1 */
        $end = $this->_hydrationType['end'];
      }
      else
      {
        $start = 1;
        $end = $this->_records['hits'];
      }
      
      for($i = $start; $i <= ($start + $end) - 1; $i++)
      {
        if($this->_records['hits'] >= $i)
        {
          $this->_records['records'][] = sfZ3950_Record::getByPosition($this->_conn, $i, $this->_hydrationMode);
        }
        else
        {
          break;
        }
      }
    }
    else
    {
      $this->_records['hits'] = 1;
      $this->_records['records'][] = sfZ3950_Record::getByPosition($this->_conn, 1, $this->_hydrationMode);
    }
  }
  
  public function getRecords()
  {
    return $this->_records['records'];
  }
  
  public function getHits()
  {
    return (int) $this->_records['hits'];
  }
  
  public function getFrom()
  {
    return $this->_parts['from'];
  }
  
  public function getWhere()
  {
    return $this->_parts['where'];
  }
  
  public function getOrderBy()
  {
    return isset($this->_parts['order']) ? $this->_parts['order']: '';
  }
  
  public function getQuery()
  {
    return $this->_parts['rpn'];
  }
  
  public function getError()
  {
    return $this->_parts['error'];
  }
  
  public function getErrorNo()
  {
    return $this->_parts['error_no'];
  }
}