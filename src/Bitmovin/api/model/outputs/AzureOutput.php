<?php


namespace Bitmovin\api\model\outputs;

use JMS\Serializer\Annotation as JMS;

class AzureOutput extends Output
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
     * AzureOutput constructor.
     * @param string $accountName
     * @param string $accountKey
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