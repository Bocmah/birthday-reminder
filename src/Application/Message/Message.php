<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Message;

use BirthdayReminder\Domain\User\UserId;
use Symfony\Component\Translation\TranslatableMessage;

final class Message
{
    public static function startedObserving(UserId $observeeId): TranslatableMessage
    {
        return new TranslatableMessage('observee.started_observing', ['%id%' => (string) $observeeId]);
    }

    public static function stoppedObserving(UserId $observeeId): TranslatableMessage
    {
        return new TranslatableMessage('observee.stopped_observing', ['%id%' => (string) $observeeId]);
    }

    public static function alreadyObserving(UserId $observeeId): TranslatableMessage
    {
        return new TranslatableMessage('observee.already_observing', ['%id%' => (string) $observeeId]);
    }

    public static function unexpectedError(): TranslatableMessage
    {
        return new TranslatableMessage('unexpected_error');
    }

    public static function userNotFoundOnThePlatform(UserId $userId): TranslatableMessage
    {
        return new TranslatableMessage('user.not_found_on_the_platform', ['%id%' => (string) $userId]);
    }

    public static function birthdayChanged(UserId $observeeId): TranslatableMessage
    {
        return new TranslatableMessage('observee.birthday_changed', ['%id%' => (string) $observeeId]);
    }

    public static function notObserving(UserId $userId): TranslatableMessage
    {
        return new TranslatableMessage('observee.not_observing', ['%id%' => (string) $userId]);
    }

    public static function notObservingAnyone(): TranslatableMessage
    {
        return new TranslatableMessage('observee.not_observing_anyone');
    }

    public static function alwaysNotify(): TranslatableMessage
    {
        return new TranslatableMessage('always_notify');
    }

    public static function notifyOnlyOnUpcomingBirthdays(): TranslatableMessage
    {
        return new TranslatableMessage('notify_only_on_upcoming_birthdays');
    }

    public static function unknownCommand(): TranslatableMessage
    {
        return new TranslatableMessage('command.unknown_command');
    }
}
