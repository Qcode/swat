<?php

@include_once 'PackageConfig.php';
if (class_exists('PackageConfig')) {
	PackageConfig::setWorkDirPosition(3);
	PackageConfig::addPackage('site');
	PackageConfig::addPackage('swat');
	PackageConfig::addPath('/so/packages/pear/pear/Date');
}

require_once 'Swat/SwatAutoloader.php';
SwatAutoloader::loadRules(dirname(__FILE__).'/../autoloader-rules.conf');
require_once '../include/DemoApplication.php';

$app = new DemoApplication('demo');
$app->title = 'Swat Demo Site';
$app->uri_prefix_length = 4;
$app->run();

?>
