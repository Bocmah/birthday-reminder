<?php

declare(strict_types=1);

namespace Vkbd\Command;

use DateTimeImmutable;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;
use Vkbd\Date\InvalidDateFormat;
use Vkbd\Observee\Observee;
use Vkbd\Observee\ObserveeStorage;
use Vkbd\Observee\ObserveeWasNotFound;
use Vkbd\Observer\Observer;
use Vkbd\Observer\ObserverId;
use Vkbd\Observer\ObserverStorage;
use Vkbd\Observer\ObserverWasNotFound;
use Vkbd\Translation\TranslatableException;
use Vkbd\Vk\Message\IncomingMessage;
use Vkbd\Vk\User\Id\AlphanumericVkId;
use Vkbd\Vk\User\Id\InvalidVkId;
use Vkbd\Vk\User\Id\NumericVkId;
use Vkbd\Vk\User\UnknownError;
use Vkbd\Vk\User\User;
use Vkbd\Vk\User\UserIsDeactivated;
use Vkbd\Vk\User\UserRetriever;
use Vkbd\Vk\User\UserWasNotFound;

use function React\Promise\all;
use function React\Promise\resolve;

final class AddObservee extends Command
{
    private const DATE_FORMAT = 'd.m.Y|';

    private const DATE_USER_FORMAT = 'DD.MM.YYYY';

    private const DATE_SEPARATOR = '.';

    private const COMMAND_FORMAT = 'add id DD.MM.YYYY';

    public function __construct(
        string $pattern,
        private ObserverStorage $observerStorage,
        private ObserveeStorage $observeeStorage,
        private UserRetriever $userRetriever
    ) {
        parent::__construct($pattern);
    }

    /**
     * @inheritDoc
     */
    public function execute(IncomingMessage $message): PromiseInterface
    {
        try {
            /**
             * @var DateTimeImmutable $birthdate
             * @var NumericVkId|AlphanumericVkId $observeeVkId
             */
            [
                'birthdate' => $birthdate,
                'id' => $observeeVkId,
            ] = $this->parseMessage($message->text());
        } catch (InvalidCommandFormat) {
            return resolve(
                Response::withTranslatableMessage('validation.command.invalid_format', ['valid_format' => self::COMMAND_FORMAT])
            );
        } catch (InvalidDateFormat) {
            return resolve(
                Response::withTranslatableMessage('validation.date.invalid_format', ['valid_format' => self::DATE_USER_FORMAT])
            );
        } catch (InvalidVkId) {
            return resolve(
                Response::withTranslatableMessage('validation.vk_id.invalid_format')
            );
        }

        return $this
            ->retrieveObservee($observeeVkId)
            ->then(fn (User $user): PromiseInterface => all([$user, $this->findOrCreateObserver($message->from())]))
            ->then(
                function (array $resolved): PromiseInterface {
                    /**
                     * @var User $vkUser
                     * @var Observer $observer
                     */
                    [$vkUser, $observer] = $resolved;

                    return all([$resolved[0], $resolved[1], $this->observeeDoesNotExist($observer->id(), $vkUser->id())]);
                }
            )
            ->then(
                function (array $resolved) use ($birthdate): PromiseInterface {
                    /**
                     * @var User $vkUser
                     * @var Observer $observer
                     */
                    [$vkUser, $observer] = $resolved;

                    return $this->observeeStorage->create(
                        $observer->id(),
                        $vkUser->id(),
                        $vkUser->fullName(),
                        $birthdate,
                    );
                }
            )
            ->then(
                static fn (QueryResult $_): Response => Response::withTranslatableMessage('observee.created', ['vk_id' => $observeeVkId->value()]),
                static fn (TranslatableException $exception): Response => new Response($exception)
            );
    }

    /**
     * @param NumericVkId|AlphanumericVkId $observeeVkId
     *
     * @return PromiseInterface<User>
     */
    private function retrieveObservee(NumericVkId|AlphanumericVkId $observeeVkId): PromiseInterface
    {
        return $this->userRetriever
            ->retrieve($observeeVkId)
            ->otherwise(
                static function (UnknownError $_) use ($observeeVkId): void {
                    throw TranslatableException::withTranslatableMessage('vk.user.retrieve.unknown_error', ['vk_id' => $observeeVkId->value()]);
                },
            )
            ->otherwise(
                static function (UserWasNotFound $_) use ($observeeVkId): void {
                    throw TranslatableException::withTranslatableMessage('vk.user.does_not_exist', ['vk_id' => $observeeVkId->value()]);
                }
            )
            ->otherwise(
                static function (UserIsDeactivated $_) use ($observeeVkId): void {
                    throw TranslatableException::withTranslatableMessage('vk.user.retrieve.deactivated', ['vk_id' => $observeeVkId->value()]);
                }
            );
    }

    private function observeeDoesNotExist(ObserverId $observerId, NumericVkId $numericVkId): PromiseInterface
    {
        return $this->observeeStorage
            ->findByObserverIdAndVkId($observerId, $numericVkId)
            ->then(
                static function (Observee $observee): void {
                    throw TranslatableException::withTranslatableMessage('observee.already_exists', ['vk_id' => $observee->vkId()->value()]);
                },
                static function (ObserveeWasNotFound $_): PromiseInterface {
                    return resolve();
                }
            );
    }

    /**
     * @param NumericVkId $vkId
     *
     * @return PromiseInterface<Observer>
     */
    private function findOrCreateObserver(NumericVkId $vkId): PromiseInterface
    {
        return $this->observerStorage
            ->findByVkId($vkId)
            ->otherwise(
                function (ObserverWasNotFound $_) use ($vkId): PromiseInterface {
                    return $this->userRetriever
                        ->retrieve($vkId)
                        ->then(fn (User $user) => $this->observerStorage->create($user->id(), $user->fullName()));
                },
            );
    }

    /**
     * @param string $message
     *
     * @return array{
     *      birthdate: DateTimeImmutable,
     *      id: NumericVkId|AlphanumericVkId
     * }
     */
    private function parseMessage(string $message): array
    {
        /** @var string[] $messageParts */
        $messageParts = explode(' ', $message);

        if (\count($messageParts) !== 3) {
            throw new InvalidCommandFormat();
        }

        return [
            'id' => $this->extractId($messageParts[1]),
            'birthdate' => $this->extractDate($messageParts[2]),
        ];
    }

    private function extractId(string $rawId): NumericVkId|AlphanumericVkId
    {
        if (is_numeric($rawId)) {
            return new NumericVkId((int) $rawId);
        }

        return new AlphanumericVkId($rawId);
    }

    private function extractDate(string $rawDate): DateTimeImmutable
    {
        $splitDate = explode(self::DATE_SEPARATOR, $rawDate);

        if (\count($splitDate) !== 3) {
            throw new InvalidDateFormat();
        }

        [$day, $month, $year] = $splitDate;

        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            throw new InvalidDateFormat();
        }

        $date = DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $rawDate);

        if ($date === false) {
            throw new InvalidDateFormat();
        }

        return $date;
    }
}
