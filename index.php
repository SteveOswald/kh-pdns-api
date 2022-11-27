<?php

$API_VERSION = "0.1";
$API_PREFIX = "/api/v1";

require __DIR__ . "/config.php";
require __DIR__ . "/vendor/autoload.php";

require __DIR__ . "/helpers/autoload.php";
require __DIR__ . "/models/autoload.php";
require __DIR__ . "/endpoints/autoload.php";

db()->connect($config["db_host"] . ":" . $config["db_port"], $config["db_database"], $config["db_username"], $config["db_password"]);

app()->run();

?>