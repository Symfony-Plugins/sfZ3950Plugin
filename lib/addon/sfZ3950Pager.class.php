<?php
/*
 * This file is part of the sfZ3950Plugin package.
 * (c) 2009 Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfZ3950_Pager
 *
 * Record.
 *
 * @package    sfZ3950Plugin
 * @author     Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version    SVN: $Id$
 */
class sfZ3950_Pager extends sfPager
{
  protected
    $page            = 1,
    $maxPerPage      = 0,
    $lastPage        = 1,
    $nbResults       = 0;
    
  public function __construct($schema, $defaultMaxPerPage = 10)
  {
    parent::__construct($schema, $defaultMaxPerPage);
    $this->setQuery(sfZ3950_Query::create()->from($schema));
  }
  
  
  public function setQuery($query)
  {
    $this->query = $query;
  }
  
  public function getQuery()
  {
    return $this->query;
  }
  
  
  public function init()
  {
    
    $count = $this->getQuery()->execute(sfZ3950_Record::TYPE_SYNTAX)->getHits();
    $this->setNbResults($count);
    
    $this->getQuery()->limit(0, $this->getMaxPerPage());
    if ($this->getPage() == 0 || $this->getMaxPerPage() == 0)
    {
      $this->setLastPage(0);
    }
    else
    {
      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $this->getQuery()->limit($offset, $this->getMaxPerPage());
    }
  }
  
  public function retrieveObject($offset)
  {
    $cForRetrieve = clone $this->getQuery();
    $cForRetrieve->limit($offset, 1);
    return $cForRetrieve->execute();
  }

  public function getResults($fetchtype = sfZ3950_Record::TYPE_RAW)
  {
    $results = $this->getQuery()->execute($fetchtype);
    return $results->getRecords();
  }
}