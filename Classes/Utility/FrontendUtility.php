<?php

declare(strict_types=1);
namespace In2code\Lux\Utility;

use In2code\Lux\Domain\Service\SiteService;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class FrontendUtility
{
    public static function getCurrentPageIdentifier(): int
    {
        return self::getTyposcriptFrontendController()->id;
    }

    /**
     * @return string "currentdomain.org"
     */
    public static function getCurrentDomain(): string
    {
        $currentDomain = GeneralUtility::getIndpEnv('HTTP_HOST');
        if ($currentDomain === null) {
            // Normally in CLI context
            $siteService = GeneralUtility::makeInstance(SiteService::class);
            $domain = $siteService->getFirstDomain();
            $currentDomain = preg_replace('~http(s)://|/~', '', $domain);
        }
        return $currentDomain;
    }

    /**
     * @return string "https://currentdomain.org"
     */
    public static function getCurrentHostAndDomain(): string
    {
        return GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
    }

    public static function isLoggedInFrontendUser(): bool
    {
        return !empty(self::getTyposcriptFrontendController()->fe_user->user['uid']);
    }

    public static function getPropertyFromLoggedInFrontendUser($propertyName = 'uid'): string
    {
        $tsfe = self::getTyposcriptFrontendController();
        if (!empty($tsfe->fe_user->user[$propertyName])) {
            return (string)$tsfe->fe_user->user[$propertyName];
        }
        return '';
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function isFrontendMode(): bool
    {
        return ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
