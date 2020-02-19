<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ConvertForm
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    private $from;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    private $to;
    /**
     * @var float
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    private $amount;

    /**
     * ConvertForm constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}