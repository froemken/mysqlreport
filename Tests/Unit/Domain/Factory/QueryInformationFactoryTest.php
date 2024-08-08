<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Domain\Factory;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Factory\QueryInformationFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class QueryInformationFactoryTest extends UnitTestCase
{
    private QueryInformationFactory $subject;

    private int $time;

    protected function setUp(): void
    {
        $this->time = time();

        $GLOBALS['EXEC_TIME'] = $this->time;

        $_SERVER = array_merge($_SERVER, [
            'REMOTE_ADDR' => '123.124.125.126',
            'HTTP_HOST' => 'example.com',
            'HTTP_REFERER' => 'https://www.typo3lexikon.de',
            'REQUEST_URI' => '/index.php',
            'SSL_SESSION_ID' => '1234567890',
        ]);

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/', 'GET', 'php://input', [], $_SERVER))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('routing', new PageArguments(1, '0', []));

        Environment::initialize(
            Environment::getContext(),
            false,
            Environment::isComposerMode(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
            Environment::getVarPath(),
            Environment::getConfigPath(),
            Environment::getCurrentScript(),
            Environment::isWindows() ? 'WINDOWS' : 'UNIX',
        );

        $this->subject = new QueryInformationFactory();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
            $GLOBALS['TYPO3_REQUEST'],
            $GLOBALS['EXEC_TIME'],
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithFrontendPid(): void
    {
        self::assertSame(
            1,
            $this->subject->createNewQueryInformation()->getPid(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithBackendPid(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/typo3/?id=30', 'GET', 'php://input', [], $_SERVER))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withQueryParams(['id' => 30]);

        $this->subject = new QueryInformationFactory();

        self::assertSame(
            30,
            $this->subject->createNewQueryInformation()->getPid(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithIp(): void
    {
        self::assertSame(
            '123.124.125.126',
            $this->subject->createNewQueryInformation()->getIp(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithReferer(): void
    {
        self::assertSame(
            'https://www.typo3lexikon.de',
            $this->subject->createNewQueryInformation()->getReferer(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithRequest(): void
    {
        self::assertSame(
            'https://example.com/index.php',
            $this->subject->createNewQueryInformation()->getRequest(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithTypo3Mode(): void
    {
        self::assertSame(
            'FE',
            $this->subject->createNewQueryInformation()->getMode(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithCallIdentifier(): void
    {
        // We are working with more entropy
        self::assertStringContainsString(
            '.',
            $this->subject->createNewQueryInformation()->getUniqueCallIdentifier(),
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithCrdate(): void
    {
        self::assertSame(
            $this->time,
            $this->subject->createNewQueryInformation()->getCrdate(),
        );
    }
}
