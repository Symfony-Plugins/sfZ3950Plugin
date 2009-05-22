<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Query
 *
 * Query.
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Query extends sfZ3950_Query_Abstract
{
  protected
    $conn,
    $cclconf = false,
    $query = '',
    $record_start = 1,
    $record_number = 1,
    $logger = null;
  
  public static function create()
  {
    return new sfZ3950_Query();
  }
}