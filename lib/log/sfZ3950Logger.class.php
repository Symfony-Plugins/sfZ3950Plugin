<?php

/*
 * This file is part of the symfony package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A symfony logging adapter for Z3950
 *
 * @package    sfZ3950Plugin
 * @subpackage log
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950Logger
{
  protected
    $dispatcher = null;

  /**
   * Constructor.
   *
   * @param sfEventDispatcher $dispatcher
   */
  public function __construct(sfEventDispatcher $dispatcher = null)
  {
    if (is_null($dispatcher))
    {
      $this->dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
    }
    else
    {
      $this->dispatcher = $dispatcher;
    }
  }

  public function log($message, $severity = sfLogger::DEBUG)
  {
    $this->dispatcher->notify(new sfEvent($this, 'application.log', array($message, 'priority' => $severity)));
  }
}
