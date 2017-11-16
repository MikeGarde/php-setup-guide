<?php

$dirs = array_filter(glob('*'), 'is_dir');

if (($key = array_search('logs', $dirs)) !== false)
{
	unset($dirs[ $key ]);
}

if (($key = array_search('php-setup-guide', $dirs)) !== false)
{
	unset($dirs[ $key ]);
}

$projects = [];

foreach ($dirs as $dir)
{
	$projects[] = $dir;
}

$projects = json_encode($projects);
$vhostsFile = file_get_contents('httpd-vhosts.conf');

preg_match_all('/^([ ]+)?ServerAlias (.*)$/m', $vhostsFile, $matches);

$vhosts = json_encode((isset($matches[2])) ? $matches[2] : []);