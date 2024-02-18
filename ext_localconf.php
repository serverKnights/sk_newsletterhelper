<?php
defined('TYPO3') || die();
(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'SkNewsletterhelper',
        'Newsletterhelper',
        [
            \ServerKnights\SkNewsletterhelper\Controller\NewsletterHelperController::class => 'list,save,resetLayout'
        ],
        // non-cacheable actions
        [
            \ServerKnights\SkNewsletterhelper\Controller\NewsletterHelperController::class => 'save'
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