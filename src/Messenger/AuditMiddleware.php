<?php


namespace App\Messenger;


use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class AuditMiddleware implements MiddlewareInterface
{

    /**
     * @var LoggerInterface
     */
    private $messengerLogger;

    public function __construct(LoggerInterface $messengerLogger)
    {
        $this->messengerLogger = $messengerLogger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null === $envelope->last(UniqueIdStamp::class)) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(UniqueIdStamp::class);
//        dump($stamp->getUniqueId());

        $context = [
          'id' => $stamp->getUniqueId(),
          'class' => get_class($envelope->getMessage())
        ];

        return $stack->next()->handle($envelope, $stack);
    }
}