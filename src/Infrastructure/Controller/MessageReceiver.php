<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Controller;

use BirthdayReminder\Domain\Messenger\IncomingMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MessageReceiver extends AbstractController
{
    #[Route('/message', methods: ['POST'])]
    public function receive(IncomingMessage $message): Response
    {

    }
}
