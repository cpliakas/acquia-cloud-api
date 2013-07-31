<?php

namespace Acquia\Cloud;

/**
 * Class that models an environment.
 */
class Environment extends \ArrayObject
{
    /**
     * Returns the name of the environment, e.g. "prod", "test", "dev".
     *
     * @return string
     */
    public function getName()
    {
        return $this['name'];
    }

    /**
     * Returns SSH host.
     *
     * @return string
     */
    public function getSshHost()
    {
        return $this['ssh_host'];
    }

    /**
     * Returns the version control system being used for this site, e.g. "svn",
     * "git". See the VCS_* constants.
     *
     * @return string
     */
    public function getVcsPath()
    {
        return $this['vcs_path'];
    }

    /**
     * Returns a  array of database clusters.
     *
     * @return array
     */
    public function getDatabaseClusters()
    {
        return $this['db_clusters'];
    }

    /**
     * Returns the name of the site in stage:username format.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
