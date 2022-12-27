<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\BirthdaysNotifier\NoUpcomingBirthdaysNotifier;
use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observer\Observer;
use BirthdayReminder\Domain\User\UserId;
use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Unit\Domain\Observer\ObserverMother;

/**
 * @covers \BirthdayReminder\Domain\BirthdaysNotifier\NoUpcomingBirthdaysNotifier
 */
final class NoUpcomingBirthdaysNotifierTest extends TestCase
{
    private readonly NoUpcomingBirthdaysNotifier $birthdaysNotifier;

    private readonly DateTimeImmutable $today;

    private readonly DateTimeImmutable $tomorrow;

    /** @var MockObject&TranslatorInterface */
    private readonly MockObject $translator;

    /** @var MockObject&Messenger */
    private readonly MockObject $messenger;

    /**
     * @test
     */
    public function notify(): void
    {
        $message = 'Сегодня и завтра дней рождения не предвидится.';

        $observer = ObserverMother::createObserverWithOneObservee();

        $this->translator
            ->method('trans')
            ->with('no_upcoming_birthdays')
            ->willReturn($message);

        $this->messenger
            ->expects($this->once())
            ->method('sendMessage')
            ->with($observer->id, $message);

        $this->birthdaysNotifier->notify($observer);
    }

    /**
     * @test
     */
    public function canNotifyPrecondition(): void
    {
        $observer = ObserverMother::createObserverWithoutObservees();

        $this->expectException(InvalidArgumentException::class);

        $this->birthdaysNotifier->notify($observer);
    }

    /**
     * @test
     *
     * @dataProvider canNotifyProvider
     *
     * @param callable(DateTimeImmutable,DateTimeImmutable):Observer $createObserver
     */
    public function canNotify(callable $createObserver, bool $canNotify): void
    {
        $this->assertSame(
            $canNotify,
            $this->birthdaysNotifier->canNotify($createObserver($this->today, $this->tomorrow))
        );
    }

    /**
     * @see canNotify()
     *
     * @return iterable<string, array{createObserver: callable(DateTimeImmutable,DateTimeImmutable):Observer, canNotify: bool}>
     */
    public function canNotifyProvider(): iterable
    {
        yield 'observer with birthdays today and tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, id: new UserId('111'), birthdate: $today);
                ObserverMother::attachObservee($observer, id: new UserId('222'), birthdate: $tomorrow);

                return $observer;
            },
            'canNotify' => false,
        ];

        yield 'observer with birthdays today' => [
            'createObserver' => static function (DateTimeImmutable $today): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: $today);

                return $observer;
            },
            'canNotify' => false,
        ];

        yield 'observer with birthdays tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $_today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: $tomorrow);

                return $observer;
            },
            'canNotify' => false,
        ];

        yield 'notifiable observer with birthdays after tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $_today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: $tomorrow->add(DateInterval::createFromDateString('1 day')));

                return $observer;
            },
            'canNotify' => true,
        ];

        yield 'notifiable observer without observees' => [
            'createObserver' => static fn (): Observer => ObserverMother::createObserverWithoutObservees(),
            'canNotify' => false,
        ];

        yield 'not notifiable observer with birthdays' => [
            'createObserver' => static function (DateTimeImmutable $today): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, id: new UserId('111'), birthdate: $today);

                $observer->toggleNotifiability();

                return $observer;
            },
            'canNotify' => false,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $calendar = $this->createMock(Calendar::class);

        $this->today = new DateTimeImmutable('13.10.2022');
        $this->tomorrow = new DateTimeImmutable('14.10.2022');

        $calendar
            ->method('today')
            ->willReturn($this->today);
        $calendar
            ->method('tomorrow')
            ->willReturn($this->tomorrow);

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->messenger = $this->createMock(Messenger::class);

        $this->birthdaysNotifier = new NoUpcomingBirthdaysNotifier($calendar, $this->translator, $this->messenger);
    }
}
