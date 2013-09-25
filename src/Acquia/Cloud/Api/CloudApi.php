<?php

namespace Acquia\Cloud\Api;

use Guzzle\Common\Collection;
use Guzzle\Http\Client;

class CloudApi extends Client
{
    const BASE_PATH = '/v1';

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Cloud\Api\CloudApi
     */
    public static function factory($config = array())
    {
        $required = array(
            'base_url',
            'username',
            'password',
        );

        $defaults = array(
            'base_url' => 'https://cloudapi.acquia.com',
            'base_path' => self::BASE_PATH,
        );

        // Instantiate the Acquia Search plugin.
        $config = Collection::fromConfig($config, $defaults, $required);
        $client = new static($config->get('base_url'), $config);
        $client->setDefaultHeaders(array(
            'Content-Type' => 'application/json; charset=utf-8',
        ));

        // Attach the Acquia Search plugin to the client.
        $plugin = new CloudApiPlugin($config->get('username'), $config->get('password'));
        $client->addSubscriber($plugin);

        return $client;
    }

    /**
     * Helper method to send a get request and return parsed JSON.
     *
     * The variables passed in the second parameter are used to expand the URI
     * expressions, which are usually the resource identifiers being requested.
     *
     * @param string $path
     * @param array $variables
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://guzzlephp.org/http-client/uri-templates.html
     */
    public function sendGet($path, $variables = array())
    {
        return $this->get(array($path, $variables))->send()->json();
    }

    /**
     * List all sites accessible by the caller.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sites()
    {
        return $this->sendGet('{+base_path}/sites.json');
    }

    /**
     * List all sites accessible by the caller.
     *
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function site($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}.json', $variables);
    }

    /**
     * List all sites accessible by the caller.
     *
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function siteDatabases($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}/dbs.json', $variables);
    }

    /**
     * List all sites accessible by the caller.
     *
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function siteDatabase($site, $database)
    {
        $variables = array(
            'site' => $site,
            'database' => $database,
        );
        return $this->sendGet('{+base_path}/sites/{site}/dbs/{database}.json', $variables);
    }

    /**
     * List all sites accessible by the caller.
     *
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environments($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}/envs.json', $variables);
    }

    /**
     * @param string $filename
     *   (optional) Wrties the JSON to the specified file.
     *
     * @return string
     */
    public function asJson($filename = null)
    {
        $data = array(
            'services' => array(
                'cloud_api' => array(
                    'class' => get_class($this),
                    'params' => array(
                        'username' => $this->getConfig('username'),
                        'password' => $this->getConfig('password'),
                    ),
                ),
            ),
        );

        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
        if (defined('JSON_PRETTY_PRINT')) {
            $options = $options | JSON_PRETTY_PRINT;
        }
        if (defined('JSON_UNESCAPED_SLASHES')) {
            $options = $options | JSON_UNESCAPED_SLASHES;
        }

        $json = json_encode($data, $options);

        if ($filename !== null) {
            file_put_contents($filename, $json);
        }

        return $json;
    }

    /**
     * @return string
     *
     * @see \Acquia\Cloud\Api\CloudApi::asJson()
     */
    public function __toString()
    {
        return $this->asJson();
    }
}
