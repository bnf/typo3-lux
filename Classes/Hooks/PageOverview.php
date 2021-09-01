<?php
namespace In2code\Lux\Hooks;

use In2code\Lux\Domain\DataProvider\PageOverview\GotinDataProvider;
use In2code\Lux\Domain\DataProvider\PagevisistsDataProvider;
use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\BackendUtility;
use In2code\Lux\Utility\ConfigurationUtility;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class PageLayoutHeader
 * to show leads in the page module in backend
 */
class PageOverview
{
    /**
     * @var string
     */
    protected $templatePathAndFile = 'EXT:lux/Resources/Private/Templates/Backend/PageOverview.html';

    /**
     * @var null
     */
    protected $visitorRepository = null;

    /**
     * @var null
     */
    protected $pagevisitRepository = null;

    /**
     * PageOverview constructor.
     * @param VisitorRepository|null $visitorRepository
     * @param PagevisitRepository|null $pagevisitRepository
     */
    public function __construct(
        VisitorRepository $visitorRepository = null,
        PagevisitRepository $pagevisitRepository = null
    ) {
        $this->visitorRepository = $visitorRepository ?: GeneralUtility::makeInstance(VisitorRepository::class);
        $this->pagevisitRepository = $pagevisitRepository ?: GeneralUtility::makeInstance(PagevisitRepository::class);
    }

    /**
     * @param array $parameters
     * @param PageLayoutController $plController
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function render(array $parameters, PageLayoutController $plController): string
    {
        unset($parameters);
        $content = '';
        if ($this->isPageOverviewEnabled($plController)) {
            $pageIdentifier = $plController->id;
            $session = BackendUtility::getSessionValue('toggle', 'PageOverview', 'General');
            $content = $this->{'render' . ucfirst(ConfigurationUtility::getPageOverviewView()) . 'View'}(
                $pageIdentifier,
                $session
            );
        }
        return $content;
    }

    /**
     * @param int $pageIdentifier
     * @param array $session
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    protected function renderAnalysisView(int $pageIdentifier, array $session): string
    {
        $filter = ObjectUtility::getFilterDto(FilterDto::PERIOD_LAST7DAYS)->setSearchterm($pageIdentifier);
        $arguments = [
            'visitors' => $this->visitorRepository->findByVisitedPageIdentifier($pageIdentifier),
            'visits' => $this->pagevisitRepository->findAmountPerPage($pageIdentifier, $filter),
            'abandons' => $this->pagevisitRepository->findAbandonsForPage($pageIdentifier, $filter),
            'delta' => $this->pagevisitRepository->compareAmountPerPage(
                $pageIdentifier,
                $filter,
                ObjectUtility::getFilterDto(FilterDto::PERIOD_7DAYSBEFORELAST7DAYS)
            ),
            'gotin' => ObjectUtility::getObjectManager()->get(GotinDataProvider::class, $filter)->get(),
            'gotout' => '',
            'numberOfVisitorsData' => ObjectUtility::getObjectManager()->get(
                PagevisistsDataProvider::class,
                ObjectUtility::getFilterDto()->setSearchterm((string)$pageIdentifier)
            ),
            'pageIdentifier' => $pageIdentifier,
            'view' => 'analysis',
            'status' => $session['status'] ?: 'show'
        ];
        return $this->getContent($arguments);
    }

    /**
     * @param int $pageIdentifier
     * @param array $session
     * @return string
     * @throws Exception
     * @noinspection PhpUnused
     */
    protected function renderLeadsView(int $pageIdentifier, array $session): string
    {
        $arguments = [
            'visitors' => $this->visitorRepository->findByVisitedPageIdentifier($pageIdentifier),
            'pageIdentifier' => $pageIdentifier,
            'view' => 'leads',
            'status' => $session['status'] ?: 'show'
        ];
        return $this->getContent($arguments);
    }

    /**
     * @param array $arguments
     * @return string
     * @throws Exception
     */
    protected function getContent(array $arguments): string
    {
        $standaloneView = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple($arguments);
        return $standaloneView->render();
    }

    /**
     * @param PageLayoutController $plController
     * @return bool
     */
    protected function isPageOverviewEnabled(PageLayoutController $plController): bool
    {
        $row = BackendUtilityCore::getRecord('pages', $plController->id, 'hidden');
        return $row['hidden'] !== 1;
    }
}
