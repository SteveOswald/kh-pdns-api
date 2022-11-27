<?php

function getRecordsFromDomainNameAndZoneRequestAndKeyHelpDnsEntry($domainName, $zoneRequest, $keyHelpDnsEntry) : KeyHelpDnsRequest {
    $changedRecords = array();

    for ($i = 0; $i < count($zoneRequest->rrsets); $i++) {
        $currentRRSet = $zoneRequest->rrsets[$i];

        if (property_exists($currentRRSet, "changetype")) {
            $changedRecords[] = RRSet::fromZoneRequestRRSet($currentRRSet);
        }
    }

    $keyHelpRecords = RRSet::arrayFromKeyHelpDnsEntry($domainName, $keyHelpDnsEntry);

    $output = new KeyHelpDnsRequest();
    $output->records = new KeyHelpDnsRequestRecords();

    $changedSoaRecordIndex = getRecordIndexForNameAndType($changedRecords, $domainName . ".", "SOA");

    if ($changedSoaRecordIndex != -1) {
        $changedSoaRecord = $changedRecords[$changedSoaRecordIndex];

        $soaRecord = new KeyHelpDnsRequestRecordsSoa();
        $soaRecord->ttl = $changedSoaRecord->ttl;

        $changedSoaRecordContent = $changedSoaRecord->records[0]->content;
        $splittedChangedSoaRecordContent = explode(" ", $changedSoaRecordContent);

        $soaRecord->primary_ns = $splittedChangedSoaRecordContent[0];
        $soaRecord->rname = $splittedChangedSoaRecordContent[1];
        $soaRecord->refresh = intval($splittedChangedSoaRecordContent[3]);
        $soaRecord->retry = intval($splittedChangedSoaRecordContent[4]);
        $soaRecord->expire = intval($splittedChangedSoaRecordContent[5]);
        $soaRecord->minimum = intval($splittedChangedSoaRecordContent[6]);

        $output->records->soa = $soaRecord;

        unset($changedRecords[$changedSoaRecordIndex]);
    } else {
        $soaRecord = new KeyHelpDnsRequestRecordsSoa();
        $soaRecord->ttl = $keyHelpDnsEntry->records->soa->ttl;

        $soaRecord->primary_ns = $keyHelpDnsEntry->records->soa->primary_ns;
        $soaRecord->rname = $keyHelpDnsEntry->records->soa->rname;
        $soaRecord->refresh = $keyHelpDnsEntry->records->soa->refresh;
        $soaRecord->retry = $keyHelpDnsEntry->records->soa->retry;
        $soaRecord->expire = $keyHelpDnsEntry->records->soa->expire;
        $soaRecord->minimum = $keyHelpDnsEntry->records->soa->minimum;

        $output->records->soa = $soaRecord;
    }

    $output->records->other = array();

    if(count($keyHelpRecords) > 0) {
        for ($i = 0; $i < count($keyHelpRecords); $i++) {
            $currentKeyHelpRecord = $keyHelpRecords[$i];
          
            if ($currentKeyHelpRecord->type == "SOA") {
            	continue;  
            }

            $changedRecordIndex = getRecordIndexForNameAndType($changedRecords, $currentKeyHelpRecord->name, $currentKeyHelpRecord->type);

            if ($changedRecordIndex != -1) {
                $changedRecord = $changedRecords[$changedRecordIndex];

                if ($changedRecord->changetype == "REPLACE") {
                    for ($x = 0; $x < count($changedRecord->records); $x++) {
                        $newRecord = new KeyHelpDnsRequestRecordsOther();
                        $newRecord->host = trim(preg_replace("/" . str_replace(".", "\\.", $domainName) . "\\.?$/", "", $changedRecord->name), ".");
                        $newRecord->ttl = $changedRecord->ttl;
                        $newRecord->type = $changedRecord->type;
                        $newRecord->value = $changedRecord->records[$x]->content;
                      
                      	if ($newRecord->host == "") {
                        	$newRecord->host = "@";  
                        }

                        $output->records->other[] = $newRecord;
                    }
                }

                unset($changedRecords[$changedRecordIndex]);
            } else {
                for ($x = 0; $x < count($currentKeyHelpRecord->records); $x++) {
                    $newRecord = new KeyHelpDnsRequestRecordsOther();
                    $newRecord->host = trim(preg_replace("/" . str_replace(".", "\\.", $domainName) . "\\.?$/", "", $currentKeyHelpRecord->name), ".");
                    $newRecord->ttl = $currentKeyHelpRecord->ttl;
                    $newRecord->type = $currentKeyHelpRecord->type;
                    $newRecord->value = $currentKeyHelpRecord->records[$x]->content;
                  
                    if ($newRecord->host == "") {
                    	$newRecord->host = "@";  
                    }

                    $output->records->other[] = $newRecord;
                }
            }
        }
    }

    foreach ($changedRecords as $index => $changedRecord) {
        if ($changedRecord->changetype == "REPLACE") {
            for ($x = 0; $x < count($changedRecord->records); $x++) {
                $newRecord = new KeyHelpDnsRequestRecordsOther();
                $newRecord->host = trim(preg_replace("/" . str_replace(".", "\\.", $domainName) . "\\.?$/", "", $changedRecord->name), ".");
                $newRecord->ttl = $changedRecord->ttl;
                $newRecord->type = $changedRecord->type;
                $newRecord->value = $changedRecord->records[$x]->content;
              
                if ($newRecord->host == "") {
                	$newRecord->host = "@";  
                }

                $output->records->other[] = $newRecord;
            }
        }
    }

    return $output;
}

function getRecordIndexForNameAndType($records, $name, $type) : int {
    foreach ($records as $index => $record) {
        if ($record->name == $name && $record->type == $type) {
            return $index;
        }
    }

    return -1;
}

?>