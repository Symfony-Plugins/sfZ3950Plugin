<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Query_OrderBy
 *
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Query_OrderBy extends sfZ3950_Query_Abstract
{
  private
    $_orderAllow = array(
      'ASC' => 'a',
      'IASC' => 'ia',
      'SASC' => 'sa',
      'DESC' => 'd',
      'IDESC' => 'id',
      'SDESC' => 'sd'
      );
    
  public function __construct($conn, $order, $parts)
  {
    $configuration = $parts['conf'];
    
    $sort_order = '';
    
    $_sort = split(' ', $order);
    
    if(count($_sort) > 1)
    {
      if((count($_sort) % 2) != 0)
      {
        throw new sfZ3950QueryException('The orderBy is not properly formatted');
      }
      else
      {
        foreach($_sort AS $key => $value)
        {
          if($key % 2 != 0)
          {
            if(!array_key_exists($value, $this->_orderAllow))
            {
              throw new sfZ3950QueryException(sprintf('The orderBy is not properly formatted. The %s is not recognized', $value));
            }
            else
            {
              $sort_order.= ' ' . $this->_orderAllow[$value];
            }
          }
          else
          {
            $sort_order.= ' ' . $configuration[$_sort[$key]];
          }
        }
      }
    }
    else
    {
      // Ascendant default
      $sort_order = $configuration[$sort[1]] . ' a';
    }
    $parts['order'] = trim($sort_order);
    yaz_sort($conn, $sort_order);
    
    parent::__construct($conn, $order, $parts);
  }
}