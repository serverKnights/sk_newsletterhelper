<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_sknewsletterhelper_domain_model_newsletterhelper', 'EXT:sk_newsletterhelper/Resources/Private/Language/locallang_csh_tx_sknewsletterhelper_domain_model_newsletterhelper.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sknewsletterhelper_domain_model_newsletterhelper');
})();
