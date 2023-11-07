<?php

declare(strict_types=1);

namespace ServerKnights\SkNewsletterhelper\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 *
 * @author Bicha Stefan 
 */
class NewsletterHelperControllerTest extends UnitTestCase
{
    /**
     * @var \ServerKnights\SkNewsletterhelper\Controller\NewsletterHelperController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\ServerKnights\SkNewsletterhelper\Controller\NewsletterHelperController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllNewsletterHelpersFromRepositoryAndAssignsThemToView(): void
    {
        $allNewsletterHelpers = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $newsletterHelperRepository = $this->getMockBuilder(\ServerKnights\SkNewsletterhelper\Domain\Repository\NewsletterHelperRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $newsletterHelperRepository->expects(self::once())->method('findAll')->will(self::returnValue($allNewsletterHelpers));
        $this->subject->_set('newsletterHelperRepository', $newsletterHelperRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('newsletterHelpers', $allNewsletterHelpers);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }
}
