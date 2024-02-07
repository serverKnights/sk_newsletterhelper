<?php
defined('TYPO3') or die('Access denied.');

include "palettes/sk_newsletterHelper_pages_palette.php";

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '
    --div--; Newsletter Helper,--palette--;;sk_newsletter_helper_palette, 
     ',
    (string)\TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT . ',' . (string)\TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_LINK. ',' . (string)\TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_SHORTCUT,
    'after:palette:descriptionColumn'
);
