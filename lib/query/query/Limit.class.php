<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Query_limit
 *
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Query_Limit extends sfZ3950_Query_Abstract
{
  public function __construct($conn, $parts)
  {
    yaz_range($conn, $parts['limit']['start'], $parts['limit']['end']);
    parent::__construct($conn, $parts['limit'], $parts);
  }
}