<?php

app()->get($API_PREFIX . "/servers/{server_id}", function ($serverId) {
    $apiKey = getApiKeyFromHeader();

    if (!checkAccess($apiKey, null)) {
        response()->exit("Unauthorized", 401);
    }
  
    $server = getServerForUserByHostnameAndApiKey($serverId, $apiKey);
  
    if ($server == null) {
        response()->exit("Not found", 404);
    }

    response()->json(
        $server
    );
});

app()->put($API_PREFIX . "/servers/{server_id}/cache/flush", function ($serverId) {
    $output = new stdClass();
    $output->count = 0;
    $output->result = "Flushed cache";
    
    response()->json(
        $output
    );
});

app()->get($API_PREFIX . "/servers", function () {
    $apiKey = getApiKeyFromHeader();

    if (!checkAccess($apiKey, null)) {
        response()->exit("Unauthorized", 401);
    }

    response()->json(
        getServersForUserByApiKey($apiKey)
    );
});

?>