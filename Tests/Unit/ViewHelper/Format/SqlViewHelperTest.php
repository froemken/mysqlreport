<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\ViewHelper\Format;

use Doctrine\SqlFormatter\HtmlHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\ViewHelper\Format\SqlViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SqlViewHelperTest extends UnitTestCase
{
    #[Test]
    public function initializeArgumentsRegistersQueryArgument(): void
    {
        $viewHelper = new SqlViewHelper();
        $arguments = $viewHelper->prepareArguments();

        self::assertArrayHasKey('query', $arguments);
        self::assertSame('string', $arguments['query']->getType());
        self::assertFalse($arguments['query']->isRequired());
        self::assertSame('', $arguments['query']->getDefaultValue());
    }

    #[Test]
    public function renderFormatsQueryUsingSqlFormatterWhenInjected(): void
    {
        $query = 'SELECT * FROM pages WHERE uid = 1';
        $sqlFormatter = new SqlFormatter(new HtmlHighlighter());

        $viewHelper = new SqlViewHelper();
        $viewHelper->setSqlFormatter($sqlFormatter);
        $viewHelper->setArguments(['query' => $query]);

        $result = $viewHelper->render();

        self::assertStringStartsWith('<pre', $result);
        self::assertStringContainsString('SELECT', $result);
        self::assertStringContainsString('pages', $result);
        self::assertStringEndsWith('</pre>', $result);
    }

    #[Test]
    public function renderEscapesHtmlSpecialCharsWhenSqlFormatterIsNotInjected(): void
    {
        $rawSqlWithScript = '<script>alert(\'xss\')</script>';
        $expectedHtml = '&lt;script&gt;alert(&apos;xss&apos;)&lt;/script&gt;';

        $viewHelper = new SqlViewHelper();
        $viewHelper->setArguments(['query' => $rawSqlWithScript]);

        self::assertSame($expectedHtml, $viewHelper->render());
    }

    #[Test]
    public function renderFormatsQueryFromRenderChildrenWhenArgumentIsNotSet(): void
    {
        $query = 'SELECT count(*) FROM fe_users';
        $sqlFormatter = new SqlFormatter(new HtmlHighlighter());

        $viewHelper = new SqlViewHelper();
        $viewHelper->setSqlFormatter($sqlFormatter);
        $viewHelper->setRenderChildrenClosure(static fn(): string => $query);

        $result = $viewHelper->render();

        self::assertStringStartsWith('<pre', $result);
        self::assertStringContainsString('SELECT', $result);
        self::assertStringContainsString('fe_users', $result);
        self::assertStringEndsWith('</pre>', $result);
    }

    #[Test]
    public function renderEscapesRenderChildrenWhenSqlFormatterIsNotInjected(): void
    {
        $query = '<script>alert(\'xss\')</script>';
        $expectedHtml = '&lt;script&gt;alert(&apos;xss&apos;)&lt;/script&gt;';

        $viewHelper = new SqlViewHelper();
        $viewHelper->setRenderChildrenClosure(static fn(): string => $query);

        self::assertSame($expectedHtml, $viewHelper->render());
    }

    /**
     * @return array<string, array{arguments: array<string, mixed>, childrenClosure: ?\Closure}>
     */
    public static function emptyQueryDataProvider(): array
    {
        return [
            'empty query argument without closure' => [
                'arguments' => ['query' => ''],
                'childrenClosure' => null,
            ],
            'whitespace query argument without closure' => [
                'arguments' => ['query' => "   \t\n  "],
                'childrenClosure' => null,
            ],
            'no query argument with closure returning empty string' => [
                'arguments' => [],
                'childrenClosure' => static fn(): string => '',
            ],
            'no query argument with closure returning whitespace' => [
                'arguments' => [],
                'childrenClosure' => static fn(): string => "  \n  ",
            ],
            'no query argument with closure returning null' => [
                'arguments' => [],
                'childrenClosure' => static fn(): ?string => null,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $arguments
     */
    #[DataProvider('emptyQueryDataProvider')]
    #[Test]
    public function renderReturnsEmptyStringWhenQueryIsEmpty(array $arguments, ?\Closure $childrenClosure): void
    {
        $sqlFormatter = new SqlFormatter(new HtmlHighlighter());

        $viewHelper = new SqlViewHelper();
        $viewHelper->setSqlFormatter($sqlFormatter);
        $viewHelper->setArguments($arguments);
        if ($childrenClosure instanceof \Closure) {
            $viewHelper->setRenderChildrenClosure($childrenClosure);
        }

        self::assertSame('', $viewHelper->render());
    }

    /**
     * @param array<string, mixed> $arguments
     */
    #[DataProvider('emptyQueryDataProvider')]
    #[Test]
    public function renderReturnsEmptyStringWithoutSqlFormatterWhenQueryIsEmpty(
        array $arguments,
        ?\Closure $childrenClosure,
    ): void {
        $viewHelper = new SqlViewHelper();
        $viewHelper->setArguments($arguments);
        if ($childrenClosure instanceof \Closure) {
            $viewHelper->setRenderChildrenClosure($childrenClosure);
        }

        self::assertSame('', $viewHelper->render());
    }
}
