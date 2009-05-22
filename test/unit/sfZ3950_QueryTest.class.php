<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(6, new lime_output_color());

$otherConfig = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
$otherContext = sfContext::createInstance($otherConfig);

$t->diag('sfZ3950_Query class');
$t->diag('->from(\'connection_name\')');
$t->diag('->where(\'au="totok" and ti="Handbuch"\')');
$t->diag('->orderBy(\'au ASC\')');
$t->diag('->limit(0,10)');

$r = sfZ3950_Query::create()
->from('connection_name')
->where('au="totok" and ti="Handbuch"')
->orderBy('au ASC')
->limit(0,10);
$res = $r->getZ3950Request();

$t->is($res['from'], 'connection_name', 'return the from parameter');
$t->is($res['where'], 'au="totok" and ti="Handbuch"', 'return the where parameter');
$t->is($res['order'], '1=1003 a', 'return the orderBy parameter');
$t->is($res['limit']['start'], 1, 'return the low limit value');
$t->is($res['limit']['end'], 10, 'return the high limit value');

$t->is($res['rpn'], '@and @attr 1=1003 totok @attr 1=4 Handbuch', 'return the rpn value');
