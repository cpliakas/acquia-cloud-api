<?php

namespace Acquia\Cloud;

/**
 * Class that models a server record.
 */
class Server extends \ArrayObject
{
    /**
     * Returns the EC2 region the server is deployed in.
     *
     * @return string
     */
    public function getRegion()
    {
        return $this['ec2_region'];
    }

    /**
     * Returns an array of services ...
     *
     * @return array
     */
    public function getServices()
    {
        return $this['services'];
    }

    /**
     * Returns the name of the server, which is it's 5th level domain name.
     *
     * @return string
     */
    public function getName()
    {
        return $this['name'];
    }

    /**
     * Returns the server's fully qualified domain name.
     *
     * @return string
     */
    public function getFqdn()
    {
        return $this['fqdn'];
    }

    /**
     * Returns the EC2 availability zone that the server is provisioned in.
     *
     * @return string
     */
    public function getAvailabilityZone()
    {
        return $this['availability_zone'];
    }


    /**
     * Returns the EC2 API type of the server.
     *
     * @return string
     */
    public function getAmiType()
    {
        return $this['ami_type'];
    }

    /**
     * Returns the name of the server, which is it's 5th level domain name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
