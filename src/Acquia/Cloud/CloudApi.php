<?php

namespace Acquia\Cloud;

use Guzzle\Http\Client;

class CloudApi
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_password;

    /**
     * Constructs a CloudApi object.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password, $endpoint = 'https://cloudapi.acquia.com/v1')
    {
        $this->_username = $username;
        $this->_password = $password;
        $this->_endpoint = $endpoint;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return new Client($this->_endpoint);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     */
    public function sendRequest($method, $path, array $data)
    {
        if (self::METHOD_GET == $method) {
            $data = array('query' => $data);
        }

        return $this->getClient()
            ->$method($path, array(), $data)
            ->setAuth($this->_username, $this->_password)
            ->send()
            ->json()
        ;
    }

    /**
     * List all sites accessible by the caller.
     *
     * @return array
     *   An array of site names in stage:unsername format.
     */
    public function getSites()
    {
        $sites = array();
        $response = $this->sendRequest(self::METHOD_GET, 'sites.json');
        foreach ($response as $site_name) {
            $sites[] = $site_name;
        }
        return $sites;
    }

    /**
     * Get a site record.
     *
     * @param string|Site $site_name
     *   The name of the site in stage:username format. A Site object can also
     *   be passed as the parameter.
     *
     * @return Site
     */
    public function getSite($site_name)
    {
        $url = 'sites/' . $site_name . '.json';
        $json = $this->sendRequest(self::METHOD_GET, $url);
        return new Site($json);
    }

    /**
     * List a site’s environments.
     *
     * @param string $site_name
     *   The name of the site in stage:username format.
     *
     * @return array
     *   An array of Environment objects keyed by name.
     */
    public function getEnvironments($site_name)
    {
        $environments = array();
        $url = 'sites/' . $site_name . '/envs.json';
        $json = $this->sendRequest(self::METHOD_GET, $url);
        foreach ($json as $environment) {
            $environments[$json['name']] = new Environment($environment);
        }
        return $environments;
    }

    /**
     * Get an environment record.
     *
     * @param string|Site $site_name
     *   The name of the site in stage:username format. A Site object can also
     *   be passed as the parameter.
     * @param string|Environment $site_name
     *   The name of the environment. An Environment object can also be passed
     *   as the parameter.
     *
     * @return Environment
     */
    public function getEnvironment($site_name, $environment_name)
    {
        $url = 'sites/' . $site_name . '/envs/' . $environment_name . '.json';
        $json = $this->sendRequest(self::METHOD_GET, $url);
        return new Environment($json);
    }

    /**
     * Get an environment record.
     *
     * @param string|Site $site_name
     *   The name of the site in stage:username format. A Site object can also
     *   be passed as the parameter.
     * @param string|Environment $site_name
     *   The name of the environment. An Environment object can also be passed
     *   as the parameter.
     * @param string $pattern
     *   An optional pattern to filter servers by. Accepts * and ?,
     *
     * @return array
     */
    public function getServers($site_name, $environment_name, $pattern = null)
    {
        $servers = array();
        $url = 'sites/' . $site_name . '/envs/' . $environment_name . '/servers.json';
        $json = $this->sendRequest(self::METHOD_GET, $url);
        foreach ($json as $server) {
            if (null === $pattern || fnmatch($pattern, $server['name'])) {
                $servers[$server['name']] = new Server($server);
            }
        }
        return $servers;
    }

    /**
     * Get an environment record.
     *
     * @param string|Site $site_name
     *   The name of the site in stage:username format. A Site object can also
     *   be passed as the parameter.
     * @param string|Environment $site_name
     *   The name of the environment. An Environment object can also be passed
     *   as the parameter.
     * @param string|Server $server_name
     *   The name of the server. A Server object can also be passed as the
     *   parameter.
     *
     * @return Server
     */
    public function getServer($site_name, $environment_name, $server_name)
    {
        $url = 'sites/' . $site_name . '/envs/' . $environment_name . '/servers/' . $server_name . '.json';
        $json = $this->sendRequest(self::METHOD_GET, $url);
        return new Server($json);
    }

    /**
     * Calculate a server’s PHP max processes record.
     *
     * @param string|Site $site_name
     *   The name of the site in stage:username format. A Site object can also
     *   be passed as the parameter.
     * @param string|Environment $site_name
     *   The name of the environment. An Environment object can also be passed
     *   as the parameter.
     * @param string|Server $server_name
     *   The name of the server. A Server object can also be passed as the
     *   parameter.
     * @param array $memory_limits
     *   An array of PHP memory limits, e.g. 64M.
     * @param array $apc_shm
     *   An array of apc shm settings, e.g. 96M.
     *
     * @return ?
     *
     * @todo Why you no work?
     */
    public function getMaxPhpProcs($site_name, $environment_name, $server_name, array $memory_limits, array $apc_shm)
    {
        $url = 'sites/' . $site_name . '/envs/' . $environment_name . '/servers/' . $server_name . '/php-procs.json';
        $data = array(
            'memory_limits' => $memory_limits,
            'apc_shm' => $apc_shm,
        );
        $json = $this->sendRequest(self::METHOD_POST, $url, $data);
        return $json;
    }
}
