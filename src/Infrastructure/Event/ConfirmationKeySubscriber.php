<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Event;

use BirthdayReminder\Infrastructure\Api\Vk\VkEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ConfirmationKeySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly string $confirmationKey)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest()->toArray();

        if (!isset($request['type'])) {
            return;
        }

        if ($request['type'] === VkEvent::Confirmation->value) {
            $event->setResponse(new Response($this->confirmationKey));
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
