<?php

namespace PMVC\PlugIn\url;

$path = __DIR__.'/../vendor/autoload.php';
include $path;
\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../../']);
