<?php

declare(strict_types=1);

namespace MezzioInstallerTest;

use MezzioInstaller\OptionalPackages;

use function count;
use function random_int;
use function str_contains;

class RequestInstallTypeTest extends OptionalPackagesTestCase
{
    private OptionalPackages $installer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installer = $this->createOptionalPackages();
    }

    public function installSelections(): array
    {
        return [
            OptionalPackages::INSTALL_MINIMAL => ['1', OptionalPackages::INSTALL_MINIMAL],
            OptionalPackages::INSTALL_FLAT    => ['2', OptionalPackages::INSTALL_FLAT],
            OptionalPackages::INSTALL_MODULAR => ['3', OptionalPackages::INSTALL_MODULAR],
        ];
    }

    /**
     * @dataProvider installSelections
     */
    public function testRequestInstallTypeReturnsExpectedConstantValue(string $selection, string $expected): void
    {
        $this->io
            ->expects($this->once())
            ->method('ask')
            ->with($this->callback(fn ($value): bool => $this->assertQueryPrompt($value)), '2')
            ->willReturn($selection);

        self::assertSame($expected, $this->installer->requestInstallType());
    }

    public function testWillContinueToPromptUntilValidAnswerPresented(): void
    {
        $tries = random_int(1, 10);

        $argumentLists = [];
        $results       = [];

        do {
            $argumentLists[] = [$this->callback(fn ($value): bool => $this->assertQueryPrompt($value)), '2'];
            $results[]       = $tries > 0 ? 'n' : '1';

            --$tries;
        } while ($tries > -1);

        $this->io
            ->expects($this->exactly(count($results)))
            ->method('ask')
            ->withConsecutive(...$argumentLists)
            ->willReturnOnConsecutiveCalls(...$results);

        $this->io
            ->expects($this->exactly(count($results) - 1))
            ->method('write')
            ->with($this->stringContains('Invalid answer'));

        self::assertSame(OptionalPackages::INSTALL_MINIMAL, $this->installer->requestInstallType());
    }

    public static function assertQueryPrompt(mixed $value): bool
    {
        self::assertIsString(
            $value,
            'Questions must be a string since symfony/console:4.0'
        );

        self::assertThat(
            str_contains($value, 'What type of installation would you like?'),
            self::isTrue(),
            'Unexpected prompt value'
        );

        return true;
    }
}
