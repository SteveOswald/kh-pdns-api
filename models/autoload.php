<?php
$audoloadDir = opendir(__DIR__);

$filesToLoad = array();

while ($tmpFile = readdir($audoloadDir)) {
	if ($tmpFile != "." && $tmpFile != ".." && $tmpFile != "autoload.php") {
      	$filesToLoad[] = $tmpFile;
	}
}

sort($filesToLoad);

for ($i = 0; $i < count($filesToLoad); $i++) {
	include_once(__DIR__ . "/" . $filesToLoad[$i]);
}

closedir($audoloadDir);
?>