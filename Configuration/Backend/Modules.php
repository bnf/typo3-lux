<?php

return [
    'lux_module' => [
        'labels' => 'LLL:EXT:lux/Resources/Private/Language/locallang_mod.xlf',
        'iconIdentifier' => 'extension-lux-module',
    ],
    'lux_LuxAnalysis' => [
        'parent' => 'lux_module',
        'position' => [],
        'access' => 'user',
        'iconIdentifier' => 'extension-lux-module-analysis',
        'path' => '/module/lux/LuxAnalysis',
        'labels' => 'LLL:EXT:lux/Resources/Private/Language/locallang_mod_analysis.xlf',
        'extensionName' => 'Lux',
        'controllerActions' => [
            \In2code\Lux\Controller\AnalysisController::class => [
                'dashboard',
                'content',
                'news',
                'linkListener',
                'search',
                'deleteLinkListener',
                'detailPage',
                'detailNews',
                'detailSearch',
                'detailDownload',
                'detailLinkListener',
                'resetFilter',
                'utm',
            ],
            \In2code\Lux\Controller\LeadController::class => [
                'dashboard',
                'list',
                'detail',
                'downloadCsv',
                'remove',
                'deactivate',
                'resetFilter',
            ],
            \In2code\Lux\Controller\GeneralController::class => [
                'information',
            ],
        ],
    ],
    'lux_LuxLead' => [
        'parent' => 'lux_module',
        'position' => [],
        'access' => 'user',
        'iconIdentifier' => 'extension-lux-module-lead',
        'path' => '/module/lux/LuxLead',
        'labels' => 'LLL:EXT:lux/Resources/Private/Language/locallang_mod_lead.xlf',
        'extensionName' => 'Lux',
        'controllerActions' => [
            \In2code\Lux\Controller\LeadController::class => [
                'dashboard',
                'list',
                'detail',
                'downloadCsv',
                'remove',
                'deactivate',
                'resetFilter',
                'companies',
                'companiesDisabled',
                'company',
                'downloadCsvCompanies',
                'removeCompany',
            ],
            \In2code\Lux\Controller\AnalysisController::class => [
                'dashboard',
                'content',
                'linkClicks',
                'detailPage',
                'detailDownload',
                'resetFilter',
            ],
            \In2code\Lux\Controller\GeneralController::class => [
                'information',
            ],
        ],
    ],
    'lux_LuxWorkflow' => [
        'parent' => 'lux_module',
        'position' => [],
        'access' => 'user',
        'iconIdentifier' => 'extension-lux-module-workflow',
        'path' => '/module/lux/LuxWorkflow',
        'labels' => 'LLL:EXT:lux/Resources/Private/Language/locallang_mod_workflow.xlf',
        'extensionName' => 'Lux',
        'controllerActions' => [
            \In2code\Lux\Controller\WorkflowController::class => [
                'list',
                'detail',
                'new',
                'create',
                'edit',
                'update',
                'delete',
                'disable',
                'enable',
                'resetFilter',
            ],
            \In2code\Lux\Controller\AbTestingController::class => [
                'list',
                'detail',
                'delete',
            ],
            \In2code\Lux\Controller\ShortenerController::class => [
                'list',
                'delete',
                'detail',
                'resetFilter',
                'qr',
            ],
            \In2code\Lux\Controller\UtmGeneratorController::class => [
                'list',
                'delete',
                'resetFilter',
            ],
            \In2code\Lux\Controller\GeneralController::class => [
                'information',
            ],
        ],
    ],
];
