<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Configuration;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use StefanFroemken\Mysqlreport\Configuration\InfoBoxConfiguration;
use StefanFroemken\Mysqlreport\InfoBox\Misc\AbortedConnectsInfoBox;

/**
 * Test case.
 */
class InfoBoxConfigurationTest extends FunctionalTestCase
{
    /**
     * @var InfoBoxConfiguration
     */
    private $subject;

    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/mysqlreport',
    ];

    protected function tearDown(): void
    {
        unset(
            $this->subject
        );
    }

    /**
     * @test
     */
    public function creatingInfoBoxWithEmptyArrayResultsInException(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->subject = new InfoBoxConfiguration([]);
    }

    /**
     * @test
     */
    public function creatingInfoBoxWithMissingPageIdentifierResultsInException(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->subject = new InfoBoxConfiguration([
            'class' => AbortedConnectsInfoBox::class
        ]);
    }

    /**
     * @test
     */
    public function creatingInfoBoxWithInvalidPageIdentifierResultsInException(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->subject = new InfoBoxConfiguration([
            'class' => \stdClass::class,
            'pageIdentifier' => 123
        ]);
    }

    /**
     * @test
     */
    public function creatingInfoBoxWithNonExistingClassResultsInException(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->subject = new InfoBoxConfiguration([
            'class' => \Stefan\Froemken\NonExistingExt\Controller\CarController::class,
            'pageIdentifier' => 'overview'
        ]);
    }

    /**
     * @test
     */
    public function creatingInfoBoxWithInvalidClassResultsInException(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->subject = new InfoBoxConfiguration([
            'class' => \stdClass::class,
            'pageIdentifier' => 'overview'
        ]);
    }

    /**
     * @test
     */
    public function setPageIdentifierWillReturnPageIdentifier(): void
    {
        $this->subject = new InfoBoxConfiguration([
            'class' => AbortedConnectsInfoBox::class,
            'pageIdentifier' => 'overview',
        ]);

        self::assertSame(
            'overview',
            $this->subject->getPageIdentifier()
        );
    }

    /**
     * @test
     */
    public function setClassWillReturnInValidObject(): void
    {
        $this->subject = new InfoBoxConfiguration([
            'class' => AbortedConnectsInfoBox::class,
            'pageIdentifier' => 'overview',
        ]);

        self::assertInstanceOf(
            AbortedConnectsInfoBox::class,
            $this->subject->getInfoBox()
        );
    }
}
