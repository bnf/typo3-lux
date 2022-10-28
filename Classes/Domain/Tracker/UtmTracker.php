<?php

declare(strict_types=1);
namespace In2code\Lux\Domain\Tracker;

use In2code\Lux\Domain\Factory\UtmFactory;
use In2code\Lux\Domain\Model\Utm;
use In2code\Lux\Domain\Repository\UtmRepository;
use In2code\Lux\Domain\Service\LogService;
use In2code\Lux\Events\NewsTrackerEvent;
use In2code\Lux\Events\PageTrackerEvent;
use Throwable;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UtmTracker
{
    protected $utmRepository = null;
    protected $logService = null;
    protected $utmFactory = null;

    public function __construct(UtmRepository $utmRepository, UtmFactory $utmFactory, LogService $logService)
    {
        $this->utmRepository = $utmRepository;
        $this->utmFactory = $utmFactory;
        $this->logService = $logService;
    }

    public function trackPage(PageTrackerEvent $event): void
    {
        if ($this->isAnyUtmParameterGiven() && $this->isNewsDetailPage() === false) {
            $utm = $this->getUtm();
            $utm->setPagevisit($event->getPagevisit());
            try {
                $this->utmRepository->add($utm);
                $this->utmRepository->persistAll();
            } catch (Throwable $exception) {
                // Do nothing
            }
        }
    }

    public function trackNews(NewsTrackerEvent $event): void
    {
        if ($this->isAnyUtmParameterGiven()) {
            $utm = $this->getUtm();
            $utm->setNewsvisit($event->getNewsvisit());
            try {
                $this->utmRepository->add($utm);
                $this->utmRepository->persistAll();
            } catch (Throwable $exception) {
                // Do nothing
            }
        }
    }

    protected function getUtm(): Utm
    {
        $parameters = [];
        foreach ($this->utmFactory->getUtmKeys() as $key) {
            $parameters[$key] = $this->getArgumentFromCurrentUrl($key);
        }
        return $this->utmFactory->get($parameters);
    }

    protected function isNewsDetailPage(): bool
    {
        $news = $this->getArgumentFromCurrentUrl('tx_news_pi1');
        return ($news['news'] ?? 0) > 0;
    }

    protected function isAnyUtmParameterGiven(): bool
    {
        $arguments = $this->getArgumentsFromCurrentUrl();
        foreach (array_keys($arguments) as $key) {
            if (in_array($key, $this->utmFactory->getUtmKeys())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    final protected function getArguments(): array
    {
        return (array)GeneralUtility::_GP('tx_lux_fe');
    }

    /**
     * Get current url form normal web request like
     * @return string
     */
    final protected function getCurrentUrl(): string
    {
        $arguments = $this->getArguments();
        if (!empty($arguments['arguments']['currentUrl'])) {
            return $arguments['arguments']['currentUrl'];
        }
        return '';
    }

    /**
     * Because the current url is passed as GET param to the AJAX request, single parameters must be parsed
     *
     * @return array
     */
    final protected function getArgumentsFromCurrentUrl(): array
    {
        $parts = parse_url($this->getCurrentUrl());
        if (isset($parts['query'])) {
            $query = $parts['query'];
            parse_str($query, $arguments);
            return $arguments;
        }
        return [];
    }

    /**
     * @param string $argumentName
     * @return string|array|null
     */
    final protected function getArgumentFromCurrentUrl(string $argumentName)
    {
        $arguments = $this->getArgumentsFromCurrentUrl();
        if (array_key_exists($argumentName, $arguments)) {
            return $arguments[$argumentName];
        }
        return null;
    }
}
