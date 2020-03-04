<?php


namespace App\Service;

use App\Service\TestService;

class ForTestService
{
    /**
     * @var array
     */
    protected $cases = [];

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function attach(TestService $service)
    {
        $this->services[] = $service;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getCases()
    {
        return $this->cases;
    }

    public function dataProviderCheck()
    {
        foreach ($this->services as $service) {
            $service->forProviderCheck();
        }
    }

    public function identicalCheck()
    {
        foreach ($this->services as $service) {
            $service->forIdenticalCheck($this);
        }
    }

    public function callbackCheck()
    {
        foreach ($this->services as $service) {
            $service->forCallbackCheck(15, 'foo bar', $this);
        };
    }

    public function consecutiveArgs()
    {
        foreach ($this->services as $service) {
           $case1 = $service->forConsecutiveArgs('foo', 8);
           $case2 = $service->forConsecutiveArgs('bar', 12);
           $this->cases[] = $case1;
           $this->cases[] = $case2;
        };
    }

    public function prophecyCheck(string $prophet)
    {
        foreach ($this->services as $service) {
            $service->forProphecyCheck($prophet);
        }
    }
}
