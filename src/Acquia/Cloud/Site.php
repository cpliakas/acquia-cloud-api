<?php

namespace Acquia\Cloud;

/**
 * Class that models a site record.
 */
class Site extends \ArrayObject
{
    const VCS_GIT = 'git';
    const VCS_SVN = 'svn';

    /**
     * Returns the human readable title of the site.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this['title'];
    }

    /**
     * Returns the name of the site in stage:username format.
     *
     * @return string
     */
    public function getName()
    {
        return $this['name'];
    }

    /**
     * Returns the hosting stage portion of the site, e.g. "devcloud"
     *
     * @return string
     */
    public function getHostingStage()
    {
        return ltrim(strstr($this['name'], ':', true), ':');
    }

    /**
     * Returns the UNIX username portion of the site.
     *
     * @return string
     */
    public function getUnixUsername()
    {
        return $this['unix_username'];
    }

    /**
     * Returns the version control system being used for this site, e.g. "svn",
     * "git". See the VCS_* constants.
     *
     * @return string
     */
    public function getVcsType()
    {
        return $this['vcs_type'];
    }

    /**
     * Returns the URL to the version control repository.
     *
     * @return string
     */
    public function getVcsUrl()
    {
        return $this['vcs_url'];
    }

    /**
     * Returns whether the site is in "production" mode.
     *
     * @return bool
     *
     * @see https://docs.acquia.com/cloud/manage
     */
    public function inProductionMode()
    {
        return (bool) $this['production_mode'];
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
