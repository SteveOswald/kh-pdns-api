<?php

app()->get($API_PREFIX . "/servers/{server_id}/zones/{zone_id}/export", function ($serverId, $zoneId) {
    $apiKey = getApiKeyFromHeader();

    if (!checkAccess($apiKey, null)) {
        response()->exit("Unauthorized", 401);
    }

    $server = getDatabaseServerForUserByHostnameAndApiKey($serverId, $apiKey);

    if ($server == null) {
        response()->exit("Not found", 404);
    }

    $domains = getDomainsForUserByApiKey($apiKey);
    $keyHelpDomains = keyHelp_getDomains($server["hostname"], $server["api_key"]);

    $userKeyHelpDomains = array();

    for ($i = 0; $i < count($domains); $i++) {
        $currentDomain = $domains[$i];

        if (array_key_exists($currentDomain["name"], $keyHelpDomains)) {
            if ($currentDomain["name"] == rtrim($zoneId, ".")) {
                $userKeyHelpDomains[$currentDomain["name"]] = $keyHelpDomains[$currentDomain["name"]];
            }
        }
    }

    $output = null;

    foreach ($userKeyHelpDomains as $domainName => $domainId) {
        $keyHelpDnsEntry = keyHelp_getDns($server["hostname"], $server["api_key"], $domainId);
        $output = Zone::getAxfrExport($server["hostname"], $domainName, $keyHelpDnsEntry);
    }

    if ($output == null) {
        response()->exit("Not found", 404);
    }

    response()->plain(
        $output
    );
});

app()->patch($API_PREFIX . "/servers/{server_id}/zones/{zone_id}", function ($serverId, $zoneId) {
    $apiKey = getApiKeyFromHeader();

    if (!checkAccess($apiKey, null)) {
        response()->exit("Unauthorized", 401);
    }

    $server = getDatabaseServerForUserByHostnameAndApiKey($serverId, $apiKey);

    if ($server == null) {
        response()->exit("Not found", 404);
    }

    $domains = getDomainsForUserByApiKey($apiKey);
    $keyHelpDomains = keyHelp_getDomains($server["hostname"], $server["api_key"]);

    $userKeyHelpDomains = array();

    for ($i = 0; $i < count($domains); $i++) {
        $currentDomain = $domains[$i];

        if (array_key_exists($currentDomain["name"], $keyHelpDomains)) {
            if ($currentDomain["name"] == rtrim($zoneId, ".")) {
                $userKeyHelpDomains[$currentDomain["name"]] = $keyHelpDomains[$currentDomain["name"]];
            }
        }
    }

    $keyHelpDomainName = null;
    $keyHelpDomainId = null;
    $keyHelpDnsEntry = null;

    foreach ($userKeyHelpDomains as $domainName => $domainId) {
        $keyHelpDnsEntry = keyHelp_getDns($server["hostname"], $server["api_key"], $domainId);
        $keyHelpDomainId = $domainId;
        $keyHelpDomainName = $domainName;
    }

    if ($keyHelpDnsEntry == null) {
        response()->exit("Not found", 404);
    }

  	$input = file_get_contents('php://input');
  
    $records = getRecordsFromDomainNameAndZoneRequestAndKeyHelpDnsEntry($keyHelpDomainName, json_decode($input), $keyHelpDnsEntry);

  	//response()->exit(json_encode($records), 200);
  
    keyHelp_putDns($server["hostname"], $server["api_key"], $domainId, $records);
  
    response()->plain("", 204);
});

app()->get($API_PREFIX . "/servers/{server_id}/zones/{zone_id}", function ($serverId, $zoneId) {
    $apiKey = getApiKeyFromHeader();

    if (!checkAccess($apiKey, null)) {
        response()->exit("Unauthorized", 401);
    }

    $server = getDatabaseServerForUserByHostnameAndApiKey($serverId, $apiKey);

    if ($server == null) {
        response()->exit("Not found", 404);
    }

    $domains = getDomainsForUserByApiKey($apiKey);
    $keyHelpDomains = keyHelp_getDomains($server["hostname"], $server["api_key"]);

    $userKeyHelpDomains = array();

    for ($i = 0; $i < count($domains); $i++) {
        $currentDomain = $domains[$i];

        if (array_key_exists($currentDomain["name"], $keyHelpDomains)) {
            if ($currentDomain["name"] == rtrim($zoneId, ".")) {
                $userKeyHelpDomains[$currentDomain["name"]] = $keyHelpDomains[$currentDomain["name"]];
            }
        }
    }

    $output = null;

    foreach ($userKeyHelpDomains as $domainName => $domainId) {
        $keyHelpDnsEntry = keyHelp_getDns($server["hostname"], $server["api_key"], $domainId);
        $output = Zone::fromKeyHelpDnsEntry($server["hostname"], $domainName, $keyHelpDnsEntry);
    }

    if ($output == null) {
        response()->exit("Not found", 404);
    }

    response()->json(
        $output
    );
});

app()->get($API_PREFIX . "/servers/{server_id}/zones", function ($serverId) {
    $apiKey = getApiKeyFromHeader();

    if (!checkAccess($apiKey, null)) {
        response()->exit("Unauthorized", 401);
    }

    $server = getDatabaseServerForUserByHostnameAndApiKey($serverId, $apiKey);

    if ($server == null) {
        response()->exit("Not found", 404);
    }

    $domains = getDomainsForUserByApiKey($apiKey);
    $keyHelpDomains = keyHelp_getDomains($server["hostname"], $server["api_key"]);

    $userKeyHelpDomains = array();

    for ($i = 0; $i < count($domains); $i++) {
        $currentDomain = $domains[$i];

        if (array_key_exists($currentDomain["name"], $keyHelpDomains)) {
            $userKeyHelpDomains[$currentDomain["name"]] = $keyHelpDomains[$currentDomain["name"]];
        }
    }

    $output = array();

    foreach ($userKeyHelpDomains as $domainName => $domainId) {
        $keyHelpDnsEntry = keyHelp_getDns($server["hostname"], $server["api_key"], $domainId);
        $output[] = Zone::fromKeyHelpDnsEntry($server["hostname"], $domainName, $keyHelpDnsEntry);
    }

    response()->json(
        $output
    );
});

?>