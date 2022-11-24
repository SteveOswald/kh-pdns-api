<?php

class Server {
    public static function fromDatabaseServer($datbaseServer) : Server {
        $output = new Server();
        $output->id = $datbaseServer["hostname"];
        $output->daemon_type = "authoritative";
        $output->version = $GLOBALS["API_VERSION"];
        $output->url = $GLOBALS["config"]["base_uri"] . $GLOBALS["API_PREFIX"] . "/servers/" . $datbaseServer["hostname"];
        $output->zones_url = $output->url . "/zones";

        return $output;
    }

    public string $type = "Server";
    public string $id;
    public string $daemon_type;
    public string $version;
    public string $url;
    public string $config_url;
    public string $zones_url;
}

?>