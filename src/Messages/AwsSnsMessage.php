<?php
namespace Lab123\AwsSns\Messages;

class AwsSnsMessage
{

    public $topicArn = "";

    public $type = "";

    public $phoneNumber = "";

    public $endpoint = "";

    public $message = "";

    public $subject = "";

    public $messageStructure = "string";

    /**
     * Create a new message instance.
     *
     * @param string $content            
     * @return void
     */
    public function __construct($message = '')
    {
        $this->message = $message;
    }

    /**
     * Set the topicArn.
     *
     * @param string $topicArn            
     * @return $this
     */
    public function topicArn($topicArn)
    {
        $this->topicArn = $topicArn;
        
        return $this;
    }

    /**
     * Set the Phone Number.
     *
     * @param string $phoneNumber            
     * @return $this
     */
    public function phoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        
        return $this;
    }

    /**
     * Set the Type.
     *
     * @param string $type            
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Set the endpoint.
     *
     * @param string $endpoint            
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        
        return $this;
    }

    /**
     * Set the message content.
     *
     * @param string $message            
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;
        
        return $this;
    }

    /**
     * Set the subject.
     *
     * @param string $subject            
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;
        
        return $this;
    }

    /**
     * Set the Message Structure.
     *
     * @param string $messageStructure            
     * @return $this
     */
    public function messageStructure($messageStructure)
    {
        $this->messageStructure = $messageStructure;
        
        return $this;
    }
}