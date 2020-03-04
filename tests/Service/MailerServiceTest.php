<?php

namespace App\Tests;

use App\Service\MailerService;
use PHPUnit\Framework\TestCase;

class MailerServiceTest extends TestCase
{
    public function testSend()
    {
        $mock = $this->createMock(\Swift_Mailer::class);
        $mock
            ->expects($this->once())
            ->method('send')
        ;
        $mailerService = new MailerService($mock);

        $messageBody = '';
        $email = 'exemple@loc.loc';
        $mailerService->send($messageBody, $email);

        $this->expectException(\Swift_RfcComplianceException::class);
        $messageBody = '';
        $email = '';
        $mailerService->send($messageBody, $email);
    }
}
