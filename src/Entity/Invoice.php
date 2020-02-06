<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripeId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripeStatus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscription", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriptionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(string $stripeId): self
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getStripeStatus(): ?string
    {
        return $this->stripeStatus;
    }

    public function setStripeStatus(string $stripeStatus): self
    {
        $this->stripeStatus = $stripeStatus;

        return $this;
    }

    public function getSubscriptionId(): ?Subscription
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(?Subscription $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }
}
