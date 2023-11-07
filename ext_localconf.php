<?php
defined('TYPO3') || die();
$GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'], [
    'NEWSLETTERCORE' => \ServerKnights\SkNewsletterhelper\ContentObject\NewsletterCoreContentObject::class
]);
(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'SkNewsletterhelper',
        'Newsletterhelper',
        [
            \ServerKnights\SkNewsletterhelper\Controller\NewsletterHelperController::class => 'list'
        ],
        // non-cacheable actions
        [
            \ServerKnights\SkNewsletterhelper\Controller\NewsletterHelperController::class => ''
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    newsletterhelper {
                        iconIdentifier = sk_newsletterhelper-plugin-newsletterhelper
                        title = LLL:EXT:sk_newsletterhelper/Resources/Private/Language/locallang_db.xlf:tx_sk_newsletterhelper_newsletterhelper.name
                        description = LLL:EXT:sk_newsletterhelper/Resources/Private/Language/locallang_db.xlf:tx_sk_newsletterhelper_newsletterhelper.description
                        tt_content_defValues {
                            CType = list
                            list_type = sknewsletterhelper_newsletterhelper
                        }
                    }
                }
                show = *
            }
       }'
    );
})();
