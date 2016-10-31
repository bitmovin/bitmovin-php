<?php


namespace Bitmovin\api\model\inputs;

use JMS\Serializer\Annotation as JMS;

class AzureInput extends Input
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $accountName;
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $accountKey;
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $container;

    /**
     * AzureInput constructor.
     * @param string $accountName
     * @param string $accountKey
     * @param string $container
     */
    public function __construct($accountName, $accountKey)
    {
        $this->accountName = $accountName;
        $this->accountKey = $accountKey;
    }

    /**
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * @param string $accountName
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;
    }

    /**
     * @return string
     */
    public function getAccountKey()
    {
        return $this->accountKey;
    }

    /**
     * @param string $accountKey
     */
    public function setAccountKey($accountKey)
    {
        $this->accountKey = $accountKey;
    }

    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }


}