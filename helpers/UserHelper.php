<?php

function getApiKeyFromHeader() : ?string {
    $headers = Leaf\Http\Headers::all();

    if (array_key_exists("X-API-Key", $headers)) {
        return $headers["X-API-Key"];
    }
  
  	if (array_key_exists("X-Api-Key", $headers)) {
        return $headers["X-Api-Key"];
    }

    if (array_key_exists("x-api-key", $headers)) {
        return $headers["x-api-key"];
    }

    return null;
}

function checkAccess($apiKey, $domain) : bool {
    if (empty($apiKey)) {
        return false;
    }

    if (count(db()->select("user")->where("api_key", $apiKey)->fetchAll()) != 1) {
        return false;
    }

    if (!empty($domain)) {
        
    }

    return true;
}

function getUserIdByApiKey($apiKey) {
    return db()->select("user")->where("api_key", $apiKey)->limit(1)->fetchObj()["id"];
}

function getDomainsForUserByApiKey($apiKey) {    
    $query = <<<EOT
        SELECT 
            domain.* 
        FROM
            user_domain,
            domain,
            user
        WHERE
            user.api_key = '$apiKey' AND
            user_domain.user_id = user.id AND
            user_domain.domain_id = domain.id
        EOT;
    
    $databaseDomains = db()->query($query)->fetchAll();

    return $databaseDomains;
}

?>