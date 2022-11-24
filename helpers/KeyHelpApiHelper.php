<?php

use Leaf\Fetch;

function keyHelp_getDomains($hostname, $apiKey) {
    $response = Fetch::request([
        "method" => "GET",
        "url" => "https://" . $hostname . "/api/v2/domains",
        "headers" => ["X-API-Key: " . $apiKey]
    ]);

    $output = array();

    for ($i = 0; $i < count($response->data); $i++) {
        $currentDomain = $response->data[$i];

        if ($currentDomain->id_parent_domain == 0) {
            $output[$currentDomain->domain] = $currentDomain->id;
        }
    }

    return $output;
}

function keyHelp_getDns($hostname, $apiKey, $domainId) {
    $response = Fetch::request([
        "method" => "GET",
        "url" => "https://" . $hostname . "/api/v2/dns/" . $domainId,
        "headers" => ["X-API-Key: " . $apiKey]
    ]);

    return $response->data;
}

function keyHelp_putDns($hostname, $apiKey, $domainId, $records) {
    $response = Fetch::request([
        "method" => "PUT",
        "url" => "https://" . $hostname . "/api/v2/dns/" . $domainId,
        "headers" => ["X-API-Key: " . $apiKey, "Content-Type: application/json"],
        "data" => json_encode($records)
    ]);

    //response()->exit(var_dump($response), 200);

    return $response->data;
}

class KeyHelpDnsRequest {
    public KeyHelpDnsRequestRecords $records;
}

class KeyHelpDnsRequestRecords {
    public KeyHelpDnsRequestRecordsSoa $soa;
    public array $other;
}

class KeyHelpDnsRequestRecordsSoa {
    public int $ttl;
    public string $primary_ns;
    public string $rname;
    public int $refresh;
    public int $retry;
    public int $expire;
    public int $minimum;
}

class KeyHelpDnsRequestRecordsOther {
    public string $host;
    public int $ttl;
    public string $type;
    public string $value;
}

?>