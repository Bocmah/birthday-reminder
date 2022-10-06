<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Controller;

use BirthdayReminder\Application\Command\CommandSelector;
use BirthdayReminder\Application\Command\ErrorDuringCommandExecution;
use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Domain\Messenger\IncomingMessage;
use BirthdayReminder\Domain\Messenger\Messenger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MessageReceiver extends AbstractController
{
    public function __construct(
        private readonly CommandSelector $commandSelector,
        private readonly TranslatorInterface $translator,
        private readonly Messenger $messenger,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/message', methods: ['POST'])]
    public function receive(IncomingMessage $message): Response
    {
        $command = $this->commandSelector->select($message->text);

        try {
            $commandResponse = $command->execute($message->from, $message->text);
        } catch (ErrorDuringCommandExecution $e) {
            $this->logger->error(
                'Error during command execution',
                [
                    'error'   => $e->getMessage(),
                    'command' => $command::class,
                    'user'    => (string) $message->from,
                    'message' => $message->text,
                ],
            );

            $commandResponse = Message::unexpectedError();
        }

        $this->messenger->sendMessage($message->from, $commandResponse->trans($this->translator));

        return new Response('ok');
    }
}
