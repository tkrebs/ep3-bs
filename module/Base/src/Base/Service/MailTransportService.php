<?php

namespace Base\Service;

use Base\Manager\ConfigManager;
use RuntimeException;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\FileOptions;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class MailTransportService extends AbstractService
{

    protected $configManager;

    protected $transport;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function getTransport()
    {
        if (! $this->transport) {
            $mailType = $this->configManager->need('mail.type');

            switch ($mailType) {
                case 'sendmail':
                    $this->transport = new Sendmail();
                    break;
                case 'smtp':
                case 'smtp-tls':
                    $optionsArray = array(
                        'host' => $this->configManager->need('mail.host'),
                        'connection_class' => $this->configManager->need('mail.auth'),
                        'connection_config' => array(
                            'username' => $this->configManager->need('mail.user'),
                            'password' => $this->configManager->need('mail.pw'),
                        ),
                    );

                    if ($mailType == 'smtp-tls') {
                        $optionsArray['port'] = 587;
                        $optionsArray['connection_config']['ssl'] = 'tls';
                    }

                    $optionPort = $this->configManager->get('mail.port');

                    if (is_numeric($optionPort)) {
                        $optionsArray['port'] = $optionPort;
                    }

                    $options = new SmtpOptions($optionsArray);

                    $this->transport = new Smtp();
                    $this->transport->setOptions($options);
                    break;
                case 'file':
                    $this->transport = new File(new FileOptions([
                        'path' => $this->configManager->get('mail.file.path', getcwd() . '/data/mails'),
                    ]));
                    break;
                default:
                    throw new RuntimeException(sprintf($this->translate('Invalid mail type %s specified'), $mailType));
            }
        }

        return $this->transport;
    }

}
