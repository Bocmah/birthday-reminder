<?php

declare(strict_types=1);

namespace BirthdayReminder\Application\Message;

use Symfony\Component\Translation\TranslatableMessage;

final class Description
{
    public static function startObserving(): TranslatableMessage
    {
        return new TranslatableMessage('command.description.start_observing');
    }

    public static function stopObserving(): TranslatableMessage
    {
        return new TranslatableMessage('command.description.stop_observing');
    }

    public static function changeBirthdate(): TranslatableMessage
    {
        return new TranslatableMessage('command.description.change_birthdate');
    }

    public static function listObservees(): TranslatableMessage
    {
        return new TranslatableMessage('command.description.list_observees');
    }

    public static function toggleNotifiability(): TranslatableMessage
    {
        return new TranslatableMessage('command.description.toggle_notifiability');
    }
}
