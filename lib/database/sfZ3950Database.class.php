<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950Database
 *
 * Provides connectivity for Z3950.
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950Database extends sfDatabase
{
  public function initialize($parameters = array())
  {
    if (!$parameters)
    {
      return;
    }
    
    parent::initialize($parameters);
    
    if(!$dsn = $this->getParameter('dsn'))
    {
      // missing required dsn parameter
      $error = 'Database configuration specifies method "dsn", but is missing dsn parameter';

      throw new sfZ3950Exception($error);
    }
    
    $this->z3950connection = sfZ3950_Manager::connection($this, $dsn, $this->getParameter('options'), $this->getParameter('name'));
  }
  
  
  public function connect()
  {
    $this->connection = $this->z3950connection;
  }


  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if ($this->connection !== null)
    {
      yaz_close($this->connection);
      $this->connection = null;
    }
  }
}