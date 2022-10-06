<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\BirthdaysNotifier;

use BirthdayReminder\Domain\BirthdaysNotifier\UpcomingBirthdaysNotifier;
use BirthdayReminder\Domain\Date\Calendar;
use BirthdayReminder\Domain\Date\DateFormatter;
use BirthdayReminder\Domain\FullName;
use BirthdayReminder\Domain\Messenger\Messenger;
use BirthdayReminder\Domain\Observee\Observee;
use BirthdayReminder\Domain\Observee\ObserveeFormatter;
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
 * @covers \BirthdayReminder\Domain\BirthdaysNotifier\UpcomingBirthdaysNotifier
 */
final class UpcomingBirthdaysNotifierTest extends TestCase
{
    private readonly UpcomingBirthdaysNotifier $birthdaysNotifier;

    private readonly DateTimeImmutable $today;

    private readonly DateTimeImmutable $tomorrow;

    /** @var MockObject&TranslatorInterface */
    private readonly MockObject $translator;

    /** @var MockObject&DateFormatter */
    private readonly MockObject $dateFormatter;

    /** @var MockObject&ObserveeFormatter */
    private readonly MockObject $observeeFormatter;

    /** @var MockObject&Messenger */
    private readonly MockObject $messenger;

    /**
     * @test
     * @dataProvider notifyProvider
     *
     * @param callable(DateTimeImmutable,DateTimeImmutable):Observer $createObserver
     */
    public function notify(callable $createObserver, string $message): void
    {
        $observer = $createObserver($this->today, $this->tomorrow);

        $this->dateFormatter
            ->method('format')
            ->willReturnCallback(static function (DateTimeImmutable $date): string {
                return $date->format('d.m');
            });

        $this->translator
            ->method('trans')
            ->willReturnCallback(static function (string $id, array $parameters): string {
                return sprintf('%s дни рождения у этих людей:', $parameters['%date%']);
            });

        $this->observeeFormatter
            ->method('format')
            ->willReturnCallback(static function (Observee $observee): string {
                return sprintf('%s %s', $observee->fullName->firstName, $observee->fullName->lastName);
            });

        $this->messenger
            ->expects($this->once())
            ->method('sendMessage')
            ->with($observer->id, $message);

        $this->birthdaysNotifier->notify($observer);
    }

    /**
     * @see notify()
     *
     * @return iterable<string, array{createObserver: callable(DateTimeImmutable,DateTimeImmutable):Observer, message: string}>
     */
    public function notifyProvider(): iterable
    {
        yield 'observer with birthdays today and tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, new UserId('111'), new FullName('John', 'Doe'), $today);
                ObserverMother::attachObservee($observer, new UserId('222'), new FullName('James', 'Dean'), $tomorrow);
                ObserverMother::attachObservee($observer, new UserId('333'), new FullName('Kate', 'Rest'), $tomorrow);

                return $observer;
            },
            'message' => '13.10 дни рождения у этих людей:\n\nJohn Doe\n\n14.10 дни рождения у этих людей:\n\nJames Dean\nKate Rest',
        ];

        yield 'observer with birthdays today' => [
            'createObserver' => static function (DateTimeImmutable $today): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, new UserId('111'), new FullName('John', 'Doe'), $today);
                ObserverMother::attachObservee($observer, new UserId('222'), new FullName('James', 'Dean'), $today);

                return $observer;
            },
            'message' => '13.10 дни рождения у этих людей:\n\nJohn Doe\nJames Dean',
        ];

        yield 'observer with birthdays tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, new UserId('111'), new FullName('John', 'Doe'), $tomorrow);
                ObserverMother::attachObservee($observer, new UserId('222'), new FullName('James', 'Dean'), $tomorrow);

                return $observer;
            },
            'message' => '14.10 дни рождения у этих людей:\n\nJohn Doe\nJames Dean',
        ];
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
            'canNotify' => true,
        ];

        yield 'observer with birthdays today' => [
            'createObserver' => static function (DateTimeImmutable $today): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: $today);

                return $observer;
            },
            'canNotify' => true,
        ];

        yield 'observer with birthdays tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: $tomorrow);

                return $observer;
            },
            'canNotify' => true,
        ];

        yield 'observer with birthdays after tomorrow' => [
            'createObserver' => static function (DateTimeImmutable $today, DateTimeImmutable $tomorrow): Observer {
                $observer = ObserverMother::createObserverWithoutObservees();
                ObserverMother::attachObservee($observer, birthdate: $tomorrow->add(DateInterval::createFromDateString('1 day')));

                return $observer;
            },
            'canNotify' => false,
        ];

        yield 'observer without observees' => [
            'createObserver' => static fn (): Observer => ObserverMother::createObserverWithoutObservees(),
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
        $this->dateFormatter = $this->createMock(DateFormatter::class);
        $this->observeeFormatter = $this->createMock(ObserveeFormatter::class);
        $this->messenger = $this->createMock(Messenger::class);

        $this->birthdaysNotifier = new UpcomingBirthdaysNotifier(
            $calendar,
            $this->translator,
            $this->dateFormatter,
            $this->observeeFormatter,
            $this->messenger,
        );
    }
}
