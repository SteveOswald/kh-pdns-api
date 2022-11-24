<?php

$API_VERSION = "0.1";
$API_PREFIX = "/api/v1";

require __DIR__ . "/config.php";
require __DIR__ . "/vendor/autoload.php";

require __DIR__ . "/helpers/autoload.php";
require __DIR__ . "/models/autoload.php";
require __DIR__ . "/endpoints/autoload.php";

db()->connect($config["db_host"] . ":" . $config["db_port"], $config["db_database"], $config["db_username"], $config["db_password"]);

if ($config["debug"]) {
    app()->get($API_PREFIX . "/debug", function () {
        response()->json(
            dns_get_record("oswald-cloud.de", DNS_SOA)
            //keyHelp_getDomains("lithium.server.ossinet.de", "L9r9MbEN.eswinlGhxQILroLhYkwWWdLKfKZ1cMSjHIGnOycN9yBDm9sf8PehutkHKVYj4W591Kqoli2Q4V0tI1wkLYvn1PRDONgmv4rINsI9FJucZtOeOEDlVmxiaqAU")
        );
    });

    app()->patch($API_PREFIX . "/debug", function () {
        response()->json(
            Leaf\Http\Headers::all()
            //keyHelp_getDomains("lithium.server.ossinet.de", "L9r9MbEN.eswinlGhxQILroLhYkwWWdLKfKZ1cMSjHIGnOycN9yBDm9sf8PehutkHKVYj4W591Kqoli2Q4V0tI1wkLYvn1PRDONgmv4rINsI9FJucZtOeOEDlVmxiaqAU")
        );
    });
}

app()->run();

?>