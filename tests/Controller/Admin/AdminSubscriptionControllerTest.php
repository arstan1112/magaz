<?php


namespace App\Tests\Controller\Admin;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminSubscriptionControllerTest extends WebTestCase
{
    public function testCancel()
    {
        $client = $this->createAuthorizedClient();
        $client->request('POST', 'subscription/cancel', [], [], [], '{"subscriptionId":"sub_GxpsQEvcXdA5po"}');

        $transport = self::$container->get('messenger.transport.async');
        $this->assertCount(1, $transport->get());
    }

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get('session');
        $person = $container->get('doctrine')->getRepository(User::class)->find(4);

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

}