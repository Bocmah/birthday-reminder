<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Http\Controller;

use BirthdayReminder\Application\Command\CommandSelector;
use BirthdayReminder\Application\Command\Exception\ErrorDuringCommandExecution;
use BirthdayReminder\Application\Message\Message;
use BirthdayReminder\Domain\Messenger\IncomingMessage;
use BirthdayReminder\Domain\Messenger\Messenger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatableInterface;
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

    #[Route('/api/v1/message', methods: ['POST'])]
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

        $this->messenger->sendMessage($message->from, $this->translateIfApplicable($commandResponse));

        return new Response('ok');
    }

    private function translateIfApplicable(string|TranslatableInterface $commandResponse): string
    {
        if ($commandResponse instanceof TranslatableInterface) {
            return $commandResponse->trans($this->translator);
        }

        return $commandResponse;
    }
}
