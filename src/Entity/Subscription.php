<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 */
class Subscription
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
     * @ORM\Column(type="datetime")
     */
    private $currentPeriodEndAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $currentPeriodStartAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", mappedBy="subscriptionId", orphanRemoval=true)
     */
    private $invoices;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $regularity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripeCustomerId;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

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

    public function getCurrentPeriodEndAt(): ?\DateTimeInterface
    {
        return $this->currentPeriodEndAt;
    }

    public function setCurrentPeriodEndAt(\DateTimeInterface $currentPeriodEndAt): self
    {
        $this->currentPeriodEndAt = $currentPeriodEndAt;

        return $this;
    }

    public function getCurrentPeriodStartAt(): ?\DateTimeInterface
    {
        return $this->currentPeriodStartAt;
    }

    public function setCurrentPeriodStartAt(\DateTimeInterface $currentPeriodStartAt): self
    {
        $this->currentPeriodStartAt = $currentPeriodStartAt;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setSubscriptionId($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getSubscriptionId() === $this) {
                $invoice->setSubscriptionId(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getRegularity(): ?string
    {
        return $this->regularity;
    }

    public function setRegularity(string $regularity): self
    {
        $this->regularity = $regularity;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }
}
