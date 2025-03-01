<?php

namespace App\Infra\Http\Rest\User\Subscriber;

use App\Domain\Shared\Model\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getData();

        if ($user instanceof UserInterface) {
            $data['id'] = $user->getId();
            $event->setData($data);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated'];
    }
}
