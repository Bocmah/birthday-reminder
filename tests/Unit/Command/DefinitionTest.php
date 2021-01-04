<?php

declare(strict_types=1);

namespace Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Vkbd\Command\Definition;

final class DefinitionTest extends TestCase
{
    /**
     * @param string $pattern
     * @param string $command
     * @param bool $expected
     *
     * @dataProvider matchProvider
     */
    public function test_it_correctly_matches(string $pattern, string $command, bool $expected): void
    {
        $definition = new Definition($pattern, static function (): void {
        });

        self::assertSame($expected, $definition->matches($command));
    }

    /**
     * @return iterable<array>
     */
    public function matchProvider(): iterable
    {
        $addRegex = '/^Add\s\S+\s\d\d\.\d\d\.\d{4}$/i';

        return [
            [$addRegex, 'add 13531412 10.11.1996', true],
            [$addRegex, 'add 2412321 08.05.2000 8', false],
            [$addRegex, 'addd 412321421 10.11.1996', false],
            ['/^delete\s\S+$/i', 'delete gsdfafrw', true],
            ['/^delete\s\S+$/i', 'delete g 432 g', false],
        ];
    }
}
