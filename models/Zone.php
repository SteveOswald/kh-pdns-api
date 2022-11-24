<?php

class Zone {
    public static function fromKeyHelpDnsEntry($serverHostname, $domainName, $keyHelpDnsEntry) : Zone {
        $output = new Zone();
        $output->id = $domainName . ".";
        $output->name = $domainName;
        $output->url = $GLOBALS["config"]["base_uri"] . $GLOBALS["API_PREFIX"] . "/servers/" . $serverHostname . "/zones/" . $domainName;
        $output->kind = "Master";
 
        $output->rrsets = RRSet::arrayFromKeyHelpDnsEntry($domainName, $keyHelpDnsEntry);

        $dnsRecord = dns_get_record($domainName, DNS_SOA);

        $output->serial = $dnsRecord[0]["serial"];
        $output->notified_serial = $dnsRecord[0]["serial"];
        $output->edited_serial = $dnsRecord[0]["serial"];
        $output->dnssec = false;

        return $output;
    }

    public static function getAxfrExport($serverHostname, $domainName, $keyHelpDnsEntry) : string {
        $zone = Zone::fromKeyHelpDnsEntry($serverHostname, $domainName, $keyHelpDnsEntry);

        $output = "";

        for ($i = 0; $i < count($zone->rrsets); $i++) {
            if ($i > 0) {
                $output = $output . "\r\n";
            }

            $RRSet = $zone->rrsets[$i];

            $output = $output . $RRSet->name . "\t" . $RRSet->ttl . "\t" . $RRSet->type . "\t" . $RRSet->records[0]->content;
        }

        return $output;
    }

    public string $id;
    public string $name;
    public string $type = "Zone";
    public string $url;
    public string $kind;
    public array $rrsets;
    public int $serial;
    public int $notified_serial;
    public int $edited_serial;
    public array $masters;
    public bool $dnssec;
    public string $nsec3param;
    public bool $nsec3narrow;
    public bool $presigned;
    public string $soa_edit;
    public string $soa_edit_api;
    public bool $api_rectify;
    public string $zone;
    public string $catalog;
    public string $account;
    public array $nameservers;
    public array $master_tsig_key_ids;
    public array $slave_tsig_key_ids;
}

?>