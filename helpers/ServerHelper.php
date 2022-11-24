<?php

function getServersForUserByApiKey($apiKey) : array {
    $output = array();

    $query = <<<EOT
        SELECT 
            server.* 
        FROM
            user_domain,
            domain,
            user,
            server
        WHERE
            user.api_key = '$apiKey' AND
            user_domain.user_id = user.id AND 
            user_domain.domain_id = domain.id AND 
            server.id = domain.server_id 
        GROUP BY 
            server.id
    EOT;

    $databaseServers = db()->query($query)->fetchAll();
    
    for ($i = 0; $i < count($databaseServers); $i++) {
        $output[] = Server::fromDatabaseServer($databaseServers[$i]);
    }

    return $output;
}

function getServerForUserByHostnameAndApiKey($hostname, $apiKey) : ?Server {
  	$databaseServer = getDatabaseServerForUserByHostnameAndApiKey($hostname, $apiKey);
  
  	if ($databaseServer == null) {
    	return null;
    }
  
  	return Server::fromDatabaseServer($databaseServer);
}

function getDatabaseServerForUserByHostnameAndApiKey($hostname, $apiKey) {
    $output = null;

    $query = <<<EOT
        SELECT 
            server.* 
        FROM
            user_domain,
            domain,
            user,
            server
        WHERE
            user.api_key = '$apiKey' AND
            user_domain.user_id = user.id AND 
            user_domain.domain_id = domain.id AND 
            server.id = domain.server_id
        GROUP BY 
            server.id
    EOT;

    $databaseServers = db()->query($query)->fetchAll();
    
    for ($i = 0; $i < count($databaseServers); $i++) {
        $currentServer = $databaseServers[$i];

        if ($currentServer["hostname"] == $hostname || ($hostname == "localhost" && $currentServer["localhost"] == 1)) {
            $output = $currentServer;
            break;
        }
    }

    return $output;
}

?>