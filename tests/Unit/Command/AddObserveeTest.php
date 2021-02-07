<?php

declare(strict_types=1);

namespace Tests\Unit\Command;

use DateTimeImmutable;
use Exception;
use React\MySQL\QueryResult;
use Tests\TestCaseWithPromisesHelpers;
use Vkbd\Command\AddObservee;
use Vkbd\Command\Response;
use Vkbd\Observee\Observee;
use Vkbd\Observee\ObserveeId;
use Vkbd\Observee\ObserveeStorage;
use Vkbd\Observee\ObserveeWasNotFound;
use Vkbd\Observer\Observer;
use Vkbd\Observer\ObserverId;
use Vkbd\Observer\ObserverStorage;
use Vkbd\Observer\ObserverWasNotFound;
use Vkbd\Person\FullName;
use Vkbd\Translation\TranslatableException;
use Vkbd\Vk\Message\IncomingMessage;
use Vkbd\Vk\User\Id\NumericVkId;
use Vkbd\Vk\User\UnknownError;
use Vkbd\Vk\User\User;
use Vkbd\Vk\User\UserIsDeactivated;
use Vkbd\Vk\User\UserRetriever;
use Vkbd\Vk\User\UserWasNotFound;

use function React\Promise\reject;
use function React\Promise\resolve;

final class AddObserveeTest extends TestCaseWithPromisesHelpers
{
    public function test_it_detects_invalid_command_format(): void
    {
        $this->assertResolvesWith(
            $this->createCommand()->execute(new IncomingMessage(new NumericVkId(123), 'add')),
            Response::withTranslatableMessage('validation.command.invalid_format', ['valid_format' => 'add id DD.MM.YYYY'])
        );
    }

    public function test_it_detects_invalid_date_format(): void
    {
        $this->assertResolvesWith(
            $this->createCommand()->execute(new IncomingMessage(new NumericVkId(123), 'add 123 13-10-1996')),
            Response::withTranslatableMessage('validation.date.invalid_format', ['valid_format' => 'DD.MM.YYYY'])
        );
    }

    public function test_it_detects_invalid_vk_id(): void
    {
        $this->assertResolvesWith(
            $this->createCommand()->execute(new IncomingMessage(new NumericVkId(123), 'add -123 13-10-1996')),
            Response::withTranslatableMessage('validation.vk_id.invalid_format')
        );
    }

    public function test_it_reports_user_retrieval_unknown_error(): void
    {
        $userRetriever = $this->createMock(UserRetriever::class);
        $userRetriever
            ->method('retrieve')
            ->willReturn(
                reject(new UnknownError())
            );

        $invalidId = 'geqfeqrfe4133';

        $this->assertResolvesWith(
            $this->createCommand(null, null, $userRetriever)->execute(new IncomingMessage(new NumericVkId(123), "add $invalidId 13.10.1996")),
            new Response(TranslatableException::withTranslatableMessage('vk.user.retrieve.unknown_error', ['vk_id' => $invalidId])),
        );
    }

    public function test_it_reports_user_retrieval_user_was_not_found(): void
    {
        $userRetriever = $this->createMock(UserRetriever::class);
        $userRetriever
            ->method('retrieve')
            ->willReturn(
                reject(new UserWasNotFound())
            );

        $invalidId = 'gwefwegweg';

        $this->assertResolvesWith(
            $this->createCommand(null, null, $userRetriever)->execute(new IncomingMessage(new NumericVkId(123), "add $invalidId 13.10.1996")),
            new Response(TranslatableException::withTranslatableMessage('vk.user.does_not_exist', ['vk_id' => $invalidId])),
        );
    }

    public function test_it_reports_user_retrieval_user_is_deactivated(): void
    {
        $userRetriever = $this->createMock(UserRetriever::class);
        $userRetriever
            ->method('retrieve')
            ->willReturn(
                reject(new UserIsDeactivated())
            );

        $invalidId = 'test';

        $this->assertResolvesWith(
            $this->createCommand(null, null, $userRetriever)->execute(new IncomingMessage(new NumericVkId(123), "add $invalidId 13.10.1996")),
            new Response(TranslatableException::withTranslatableMessage('vk.user.retrieve.deactivated', ['vk_id' => $invalidId])),
        );
    }

    public function test_it_creates_observer_if_it_does_not_exist(): void
    {
        $observeeVkId = new NumericVkId(100);
        $observerVkId = new NumericVkId(1300);
        $observerName = new FullName('Jack', 'Daniels');

        $userRetriever = $this->createMock(UserRetriever::class);
        $userRetriever
            ->method('retrieve')
            ->willReturnOnConsecutiveCalls(
                // Observee
                resolve(new User($observeeVkId, new FullName('John', 'Doe'))),
                // Observer
                resolve(new User($observerVkId, $observerName))
            );

        $observerStorage = $this->createMock(ObserverStorage::class);
        $observerStorage
            ->method('findByVkId')
            ->willReturn(
                reject(new ObserverWasNotFound())
            );

        $observerStorage->expects(self::once())
            ->method('create')
            ->with($observerVkId, $observerName, true);

        $this
            ->createCommand($observerStorage, null, $userRetriever)
            ->execute(new IncomingMessage($observerVkId, "add {$observeeVkId->value()} 13.10.1996"));
    }

    /**
     * @throws Exception
     */
    public function test_it_reports_already_existing_observee(): void
    {
        $observeeVkId = new NumericVkId(100);
        $observeeName = new FullName('John', 'Doe');
        $observeeBirthdate = '13.10.1996';

        $observerId = new ObserverId(3);
        $observerVkId = new NumericVkId(1300);
        $observerName = new FullName('Jack', 'Daniels');

        $userRetriever = $this->createMock(UserRetriever::class);
        $userRetriever
            ->method('retrieve')
            ->willReturnOnConsecutiveCalls(
                // Observee
                resolve(new User($observeeVkId, $observeeName)),
                // Observer
                resolve(new User($observerVkId, $observerName))
            );

        $observeeStorage = $this->createMock(ObserveeStorage::class);
        $observeeStorage
            ->method('findByObserverIdAndVkId')
            ->willReturn(
                resolve(
                    new Observee(
                        new ObserveeId(3),
                        $observeeVkId,
                        $observeeName,
                        new DateTimeImmutable($observeeBirthdate),
                        $observerId
                    )
                )
            );

        $observerStorage = $this->createMock(ObserverStorage::class);
        $observerStorage
            ->method('findByVkId')
            ->willReturn(
                resolve(
                    new Observer(
                        $observerId,
                        $observerVkId,
                        $observerName,
                    )
                )
            );

        $this->assertResolvesWith(
            $this
                ->createCommand($observerStorage, $observeeStorage, $userRetriever)
                ->execute(
                    new IncomingMessage(
                        $observerVkId,
                        "add {$observeeVkId->value()} $observeeBirthdate"
                    )
                ),
            new Response(TranslatableException::withTranslatableMessage('observee.already_exists', ['vk_id' => $observeeVkId->value()])),
        );
    }

    /**
     * @throws Exception
     */
    public function test_it_creates_observee(): void
    {
        $observeeVkId = new NumericVkId(100);
        $observeeName = new FullName('John', 'Doe');
        $observeeBirthdate = '13.10.1996';

        $observerId = new ObserverId(3);
        $observerVkId = new NumericVkId(1300);
        $observerName = new FullName('Jack', 'Daniels');

        $userRetriever = $this->createMock(UserRetriever::class);
        $userRetriever
            ->method('retrieve')
            ->willReturnOnConsecutiveCalls(
                // Observee
                resolve(new User($observeeVkId, $observeeName)),
                // Observer
                resolve(new User($observerVkId, $observerName))
            );

        $observeeStorage = $this->createMock(ObserveeStorage::class);
        $observeeStorage
            ->method('findByObserverIdAndVkId')
            ->willReturn(
                reject(ObserveeWasNotFound::withObserverIdAndVkId($observerId, $observeeVkId))
            );
        $observeeStorage
            ->expects(self::once())
            ->method('create')
            ->with(
                $observerId,
                $observeeVkId,
                $observeeName,
                new DateTimeImmutable($observeeBirthdate)
            )
            ->willReturn(resolve(new QueryResult()));

        $observerStorage = $this->createMock(ObserverStorage::class);
        $observerStorage
            ->method('findByVkId')
            ->willReturn(
                resolve(
                    new Observer(
                        $observerId,
                        $observerVkId,
                        $observerName,
                    )
                )
            );

        $this->assertResolvesWith(
            $this
                ->createCommand($observerStorage, $observeeStorage, $userRetriever)
                ->execute(
                    new IncomingMessage(
                        $observerVkId,
                        "add {$observeeVkId->value()} $observeeBirthdate"
                    )
                ),
            Response::withTranslatableMessage('observee.created', ['vk_id' => $observeeVkId->value()]),
        );
    }

    private function createCommand(?ObserverStorage $observerStorage = null, ?ObserveeStorage $observeeStorage = null, ?UserRetriever $userRetriever = null): AddObservee
    {
        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        return new AddObservee(
            '/Add\s\S+\s\d\d\.\d\d\.\d{4}/i',
            $observerStorage ?? $this->createMock(ObserverStorage::class),
            $observeeStorage ?? $this->createMock(ObserveeStorage::class),
            $userRetriever ?? $this->createMock(UserRetriever::class),
        );
    }
}
