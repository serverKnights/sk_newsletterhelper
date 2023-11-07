<?php

declare(strict_types=1);

namespace ServerKnights\SkNewsletterhelper\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author Bicha Stefan 
 */
class NewsletterHelperTest extends UnitTestCase
{
    /**
     * @var \ServerKnights\SkNewsletterhelper\Domain\Model\NewsletterHelper|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \ServerKnights\SkNewsletterhelper\Domain\Model\NewsletterHelper::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function dummyTestToNotLeaveThisFileEmpty(): void
    {
        self::markTestIncomplete();
    }
}
