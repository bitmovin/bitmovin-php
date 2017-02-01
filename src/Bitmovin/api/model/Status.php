<?php


namespace Bitmovin\api\model;

use JMS\Serializer\Annotation as JMS;

class Status extends AbstractModel
{

    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $eta;
    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $progress;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $status;
    /**
     * @JMS\Type("array")
     * @var  array
     */
    private $messages;
    /**
     * @JMS\Type("array")
     * @var  array
     */
    private $subtasks;

    /**
     * @return float
     */
    public function getEta()
    {
        return $this->eta;
    }

    /**
     * @param float $eta
     */
    public function setEta($eta)
    {
        $this->eta = $eta;
    }

    /**
     * @return float
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param float $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getSubtasks()
    {
        return $this->subtasks;
    }

    /**
     * @param array $subtasks
     */
    public function setSubtasks($subtasks)
    {
        $this->subtasks = $subtasks;
    }


}