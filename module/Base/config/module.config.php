<?php

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'AjaxViewModel' => 'Base\Controller\Plugin\AjaxViewModel',
            'DefaultViewModel' => 'Base\Controller\Plugin\DefaultViewModel',
            'JsonViewModel' => 'Base\Controller\Plugin\JsonViewModel',
        ),

        'factories' => array(
            'Config' => 'Base\Controller\Plugin\ConfigFactory',
            'Cookie' => 'Base\Controller\Plugin\CookieFactory',
            'DateFormat' => 'Base\Controller\Plugin\DateFormatFactory',
            'NumberFormat' => 'Base\Controller\Plugin\NumberFormatFactory',
            'Option' => 'Base\Controller\Plugin\OptionFactory',
            'RedirectBack' => 'Base\Controller\Plugin\RedirectBackFactory',
            'Translate' => 'Base\Controller\Plugin\TranslateFactory',
        ),

        'aliases' => array(
            'T' => 'Translate',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Base\Manager\ConfigManager' => 'Base\Manager\ConfigManagerFactory',
            'Base\Manager\OptionManager' => 'Base\Manager\OptionManagerFactory',

            'Base\Table\OptionTable' => 'Base\Table\OptionTableFactory',

            'Base\Service\MailService' => 'Base\Service\MailServiceFactory',
            'Base\Service\MailTransportService' => 'Base\Service\MailTransportServiceFactory',

            'MvcTranslator' => 'Base\I18n\Translator\TranslatorFactory',

            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',

            /* Listeners */

            'Base\Manager\Listener\ConfigLocaleListener' => 'Base\Manager\Listener\ConfigLocaleListenerFactory',
        ),

        'initializers' => array(
            'AbstractManager' => 'Base\Manager\AbstractManagerInitializer',
            'AbstractService' => 'Base\Service\AbstractServiceInitializer',
        ),

        'aliases' => array(
            'Translator' => 'MvcTranslator',
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'AjaxAwareScript' => 'Base\View\Helper\AjaxAwareScript',
            'DateRange' => 'Base\View\Helper\DateRange',

            'FormDefault' => 'Base\View\Helper\FormDefault',
            'FormElementErrors' => 'Base\View\Helper\FormElementErrors',
            'FormElementNotes' => 'Base\View\Helper\FormElementNotes',
            'FormRowCheckbox' => 'Base\View\Helper\FormRowCheckbox',
            'FormRowCompact' => 'Base\View\Helper\FormRowCompact',
            'FormRowDefault' => 'Base\View\Helper\FormRowDefault',
            'FormRowSubmit' => 'Base\View\Helper\FormRowSubmit',

            'Links' => 'Base\View\Helper\Links',
            'Message' => 'Base\View\Helper\Message',
            'PrettyDate' => 'Base\View\Helper\PrettyDate',
            'PrettyTime' => 'Base\View\Helper\PrettyTime',
            'Setup' => 'Base\View\Helper\Setup',
            'TimeFormat' => 'Base\View\Helper\TimeFormat',
            'TimeRange' => 'Base\View\Helper\TimeRange',

            /* Layout */

            'HeaderAttributes' => 'Base\View\Helper\Layout\HeaderAttributes',
        ),

        'factories' => array(
            'Config' => 'Base\View\Helper\ConfigFactory',

            'CurrencyFormat' => 'Base\View\Helper\CurrencyFormatFactory',
            'DateFormat' => 'Base\View\Helper\DateFormatFactory',
            'NumberFormat' => 'Base\View\Helper\NumberFormatFactory',

            'PriceFormat' => 'Base\View\Helper\PriceFormatFactory',

            'Messages' => 'Base\View\Helper\MessagesFactory',

            'Option' => 'Base\View\Helper\OptionFactory',

            'Tabs' => 'Base\View\Helper\TabsFactory',

            /* Layout */

            'HeaderLocaleChoice' => 'Base\View\Helper\Layout\HeaderLocaleChoiceFactory',
        ),

        'aliases' => array(
            'T' => 'Translate',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'base/layout'          => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'            => __DIR__ . '/../view/error/404.phtml',
            'error/500'            => __DIR__ . '/../view/error/500.phtml',
        ),

        'layout'                   => 'base/layout',

        'display_exceptions'       => EP3_BS_DEV,
        'exception_template'       => 'error/500',

        'display_not_found_reason' => EP3_BS_DEV,
        'not_found_template'       => 'error/404',

        'doctype'                  => 'HTML5',
    ),
);
