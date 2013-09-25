<?php

namespace Acquia\Cloud\Api;

use Guzzle\Common\Collection;
use Guzzle\Http\Client;
use Zend\Json\Json;

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
     * Encodes JSON.
     *
     * @param smixed $data
     *
     * @return string
     */
    public static function jsonEncode($data)
    {
        return Json::prettyPrint(Json::encode($data), array('indent' => '    '));
    }

    /**
     * Helper method to send a GET request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
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
     * Helper method to send a POST request and return parsed JSON.
     *
     * The variables passed in the second parameter are used to expand the URI
     * expressions, which are usually the resource identifiers being requested.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param mixed $body
     *   Defaults to null. If a non-string is passed then the data is converted
     *   to JSON.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://guzzlephp.org/http-client/uri-templates.html
     */
    public function sendPost($path, $variables = array(), $body = null)
    {
        if (!is_string($body)) {
            $body = self::jsonEncode($body);
        }
        return $this->post(array($path, $variables), null, $body)->send()->json();
    }

    /**
     * Helper method to send a DELETE request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://guzzlephp.org/http-client/uri-templates.html
     */
    public function sendDelete($path, $variables = array())
    {
        return $this->delete(array($path, $variables))->send()->json();
    }

    /**
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sites()
    {
        return $this->sendGet('{+base_path}/sites.json');
    }

    /**
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
     * @param string $site
     * @param string $env
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environments($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environment($site, $env, $type)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'type' => $type,
        );
        return $this->sendPost('{+base_path}/sites/{site}/envs/{env}/install/{type}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $environment
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function installDistro($site, $environment)
    {
        $variables = array(
            'site' => $site,
            'environment' => $environment,
        );
        return $this->sendPost('{+base_path}/sites/{site}/envs.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function servers($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/servers.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $server
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function server($site, $env, $server)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'server' => $server,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/servers/{server}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $server
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function maxPhpProcs($site, $env, $server)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'server' => $server,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/servers/{server}/php-procs.json', $variables);
    }

    /**
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sshKeys($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}/sshkeys.json', $variables);
    }

    /**
     * @param string $site
     * @param int $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sshKey($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        return $this->sendGet('{+base_path}/sites/{site}/sshkeys/{id}.json', $variables);
    }

    /**
     * @param type $site
     * @param type $publicKey
     * @param type $nickname
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function addSshKey($site, $publicKey, $nickname)
    {
        $path = '{+base_path}/sites/{site}/sshkeys.json?nickname={nickname}';
        $variables = array(
            'site' => $site,
            'nickname' => $nickname,
        );
        $body = array('ssh_pub_key' => $publicKey);
        return $this->sendPost($path, $variables, $body);
    }

    /**
     * @param string $site
     * @param int $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function deleteSshKey($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        return $this->sendDelete('{+base_path}/sites/{site}/sshkeys/{id}.json', $variables);
    }

    /**
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function svnUsers($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}/svnusers.json', $variables);
    }

    /**
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function svnUser($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        return $this->sendGet('{+base_path}/sites/{site}/svnusers/{id}.json', $variables);
    }

    /**
     * @param type $site
     * @param type $publicKey
     * @param type $nickname
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @todo Testing returned a 400 response.
     */
    public function addSvnUser($site, $username, $password)
    {
        $path = '{+base_path}/sites/{site}/svnusers/{username}.json';
        $variables = array(
            'site' => $site,
            'username' => $username,
        );
        $body = array('password' => $password);
        return $this->sendPost($path, $variables, $body);
    }

    /**
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @todo Testing returned a 400 response.
     */
    public function deleteSvnUser($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        return $this->sendDelete('{+base_path}/sites/{site}/svnusers/{id}.json', $variables);
    }

    /**
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
     * @param string $site
     * @param string $db
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function siteDatabase($site, $db)
    {
        $variables = array(
            'site' => $site,
            'db' => $db,
        );
        return $this->sendGet('{+base_path}/sites/{site}/dbs/{db}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environmentDatabases($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environmentDatabase($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function dabaseBackups($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param string $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function dabaseBackup($site, $env, $db, $id)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $id,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param string $id
     *
     * @return \Guzzle\Http\Message\Response
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function downloadDabaseBackup($site, $env, $db, $id)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $id,
        );
        return $this->get('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}/download.json', $variables)->send();
    }

    /**
     * @param string $filename
     *   (optional) Wrties the JSON to the specified file.
     *
     * @return string
     */
    public function asJson($filename = null)
    {
        $data = self::jsonEncode(array(
            'services' => array(
                'cloud_api' => array(
                    'class' => get_class($this),
                    'params' => array(
                        'username' => $this->getConfig('username'),
                        'password' => $this->getConfig('password'),
                    ),
                ),
            ),
        ));

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
