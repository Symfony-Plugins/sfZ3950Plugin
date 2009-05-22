<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Record
 *
 * Record.
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Record
{
  protected
    static $_allowHydrationMode = array(
      'string',
      'xml',
      'raw',
      'syntax',
      'array'
    );
    
  const
    TYPE_STRING   = 'string',
    TYPE_XML      = 'xml',
    TYPE_RAW      = 'raw',
    TYPE_SYNTAX   = 'syntax',
    TYPE_ARRAY    = 'array';
    
  public static function getByPosition($conn, $position, $hydrationMode)
  {
    return yaz_record($conn, $position, $hydrationMode);
  }
  
  public static function getAllowHydrationMode()
  {
    return self::$_allowHydrationMode;
  }
}