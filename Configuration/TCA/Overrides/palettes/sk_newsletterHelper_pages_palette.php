<?php
//TODO move all the strings into language files
$tca_newsletter_helper_palette = [
    'palettes' => [
        'sk_newsletter_helper_palette' => [
            'label' => 'NewsletterHelper Plugin',
            'showitem' => 'tx_sk_newsletter_helper_is_newsletter, --linebreak--, Newsletter Settings,tx_sk_newsletter_helper_layout_select',
        ],
    ],
    'columns' => [
      #  'tx_sk_newsletter_helper_is_newsletter' => [
      #      'exclude' => false,
      #      'label' => 'Is Newsletter Page',
      #      'onChange' => 'reload',
      #      'config' => [
      #          'type' => 'check',
      #          'default' => 0,
      #      ],
      #  ],

        'tx_sk_newsletter_helper_layout_select' => [
            'exclude' => false,
            'label' => 'Ausgewähltes Layout',
            //'displayCond' => 'FIELD:tx_sk_newsletter_helper_is_newsletter:REQ:true',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('Newsletter.html', "EXT:sk_newsletterhelper/Resources/Private/Templates/Newsletter.html"),
                )
            ],
        ],

    ],
];

$GLOBALS['TCA']['pages'] = array_replace_recursive($GLOBALS['TCA']['pages'], $tca_newsletter_helper_palette);


/*
 *
 */