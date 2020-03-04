<?php

namespace App\Tests;

use App\Service\ForTestService;
use App\Service\TestService;
use App\Stripe\PaymentGateway;
use PHPUnit\Framework\TestCase;

class TestServiceTest extends TestCase
{
    /**
     * @dataProvider getArgs
     * @param string $data
     */
    public function testProviders(string $data)
    {
        $mock = $this->getMockBuilder(TestService::class)
            ->setMethods([$data])
            ->getMock();

        $mock->expects($this->once())
            ->method($data);

        $gateway = new ForTestService('New Gateway');
        $gateway->attach($mock);
        $gateway->dataProviderCheck();
    }

    public function testMethodExpectsIdentical()
    {
        $gateway = new ForTestService('New Gateway');

        $mock = $this->getMockBuilder(TestService::class)
            ->setMethods(['forIdenticalCheck'])
            ->getMock();

        $mock->expects($this->once())
            ->method('forIdenticalCheck')
            ->with(
                $this->identicalTo($gateway)
            );

        $gateway->attach($mock);
        $gateway->identicalCheck();
    }

    public function testCallback()
    {
        $mock = $this->getMockBuilder(TestService::class)
            ->setMethods(['forCallbackCheck'])
            ->getMock();

        $mock->expects($this->once())
            ->method('forCallbackCheck')
            ->with(
                $this->greaterThan(0),
                $this->stringContains('foo'),
                $this->callback(function ($gateway)
                {
                    return is_callable([$gateway, 'getName']) &&
                        $gateway->getName() == 'New Gateway';
                }
            ));

        $gateway = new ForTestService('New Gateway');

        $gateway->attach($mock);
        $gateway->callbackCheck();
    }

    public function testConsecutiveArgs()
    {
        $mock = $this->getMockBuilder(TestService::class)
            ->setMethods(['forConsecutiveArgs'])
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('forConsecutiveArgs')
            ->withConsecutive(
                [$this->equalTo('foo'), $this->greaterThan(0)],
                [$this->equalTo('bar'), $this->greaterThan(0)]
            )
//            ->willReturn(new TestService())
        ;

        $gateway = new ForTestService('New Gateway');

        $gateway->attach($mock);
        $gateway->consecutiveArgs();
        dump($gateway->getServices());
        dump($gateway->getCases());
    }

    public function testProphecy()
    {
        $gateway = new ForTestService('New Gateway');

        $service = $this->prophesize(TestService::class);
        $service
            ->forProphecyCheck('prophet')
            ->shouldBeCalled();

        $gateway->attach($service->reveal());
        $gateway->prophecyCheck('prophet');
    }

    public function getArgs()
    {
        return [
            ['forProviderCheck'],
        ];
    }
}
