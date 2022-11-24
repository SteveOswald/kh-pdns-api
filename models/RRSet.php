<?php

class RRSet {
    public static function arrayFromKeyHelpDnsEntry($domainName, $keyHelpDnsEntry) : array {
        $output = array();

        $keyHelpRecords = $keyHelpDnsEntry->records;

        $output[] = RRSet::getSoaRecord($domainName, $keyHelpRecords->soa);

        $otherKeyHelpRecords = $keyHelpRecords->other;

        for ($i = 0; $i < count($otherKeyHelpRecords); $i++) {
            $currentKeyHelpRecord = $otherKeyHelpRecords[$i];

            $currentRecordName = null;

            if ($currentKeyHelpRecord->host == "@") {
                $currentRecordName = $domainName . ".";
            } else {
                $currentRecordName = $currentKeyHelpRecord->host . "." . $domainName . ".";
            }
            
            $currentRecordIndex = getRecordIndexForNameAndType($output, $currentRecordName, $currentKeyHelpRecord->type);

            if ($currentRecordIndex != -1) {
                $tmpRRSet = $output[$currentRecordIndex];

                $tmpRecord = new Record();
                $tmpRecord->disabled = false;
                $tmpRecord->content = $currentKeyHelpRecord->value;

                $tmpRRSet->records[] = $tmpRecord;
            } else {
                $tmpRRSet = new RRSet();

                $tmpRRSet->name = $currentRecordName;
                $tmpRRSet->type = $currentKeyHelpRecord->type;
                $tmpRRSet->ttl = $currentKeyHelpRecord->ttl;
                
                $tmpRRSet->records = array();

                $tmpRecord = new Record();
                $tmpRecord->disabled = false;
                $tmpRecord->content = $currentKeyHelpRecord->value;

                $tmpRRSet->records[] = $tmpRecord;

                $output[] = $tmpRRSet;
            }
        }

        return $output;
    }

    public static function fromZoneRequestRRSet($zoneRequestRRSet) : RRSet {
        $output = new RRSet();

        $output->name = $zoneRequestRRSet->name;
        $output->type = $zoneRequestRRSet->type;
        $output->ttl = $zoneRequestRRSet->ttl;

        if (property_exists($zoneRequestRRSet, "changetype")) {
            $output->changetype = $zoneRequestRRSet->changetype;
        }

        if (property_exists($zoneRequestRRSet, "records")) {
            if ($zoneRequestRRSet->records != null && count($zoneRequestRRSet->records) > 0) {
                $output->records = array();

                for ($i = 0; $i < count($zoneRequestRRSet->records); $i++) {
                    $currentRecord = $zoneRequestRRSet->records[$i];

                    if (!$currentRecord->disabled) {
                        $record = new Record();
                        $record->disabled = false;
                        $record->content = $currentRecord->content;

                        $output->records[] = $record;
                    }
                }
            }
        }

        return $output;
    }

    private static function getSoaRecord($domainName, $soaRecord) : RRSet {
        $output = new RRSet();

        $output->name = $domainName . ".";
        $output->type = "SOA";
        $output->ttl = $soaRecord->ttl;

        $dnsRecord = dns_get_record($domainName, DNS_SOA);

        $output->serial = $dnsRecord[0]["serial"];

        $recordString = $soaRecord->primary_ns . " " . 
            $soaRecord->rname . " " . 
            $dnsRecord[0]["serial"] . " " .
            $soaRecord->refresh . " " . 
            $soaRecord->retry . " " .
            $soaRecord->expire . " " .
            $soaRecord->minimum;
        
        $output->records = array();

        $record = new Record();
        $record->disabled = false;
        $record->content = $recordString;

        $output->records[] = $record;

        return $output;
    }

    public string $name;
    public string $type;
    public int $ttl;
    public string $changetype;
    public array $records;
    public array $comments;
}

?>