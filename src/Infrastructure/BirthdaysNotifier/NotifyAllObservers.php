<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\BirthdaysNotifier;

use BirthdayReminder\Application\BirthdaysNotifierService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'observers:notify', description: 'Notify all observers about upcoming birthdays.')]
final class NotifyAllObservers extends Command
{
    public function __construct(private readonly BirthdaysNotifierService $notifierService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifierService->notifyObservers();

        return Command::SUCCESS;
    }
}
