<?php

namespace Backend\Service;

use Base\Manager\ConfigManager;
use Base\Manager\OptionManager;
use Base\Service\AbstractService;
use Base\Service\MailService as BaseMailService;

class MailService extends AbstractService
{

    protected $baseMailService;
    protected $configManager;
    protected $optionManager;

    public function __construct(BaseMailService $baseMailService, ConfigManager $configManager, OptionManager $optionManager)
    {
        $this->baseMailService = $baseMailService;
        $this->configManager = $configManager;
        $this->optionManager = $optionManager;
    }

    public function send($subject, $text, array $attachments = array(), $addendum = null)
    {
        $fromAddress = $this->configManager->need('mail.address');
        $fromName = $this->optionManager->need('client.name.short') . ' ' . $this->optionManager->need('service.name.full');

        $replyToAddress = null;
        $replyToName = null;

        $toAddress = $this->optionManager->need('client.contact.email');
        $toName = $this->optionManager->need('client.name.full');

        $text = sprintf("%s,\r\n\r\n%s\r\n\r\n%s %s\r\n\r\n%s,\r\n%s %s\r\n%s",
            $this->t('Hello'),
            $text,
            $this->t('This was an automated message from the system.'),
            $addendum,
            $this->t('Sincerely'),
            $this->t("Your"),
            $this->optionManager->need('service.name.full'),
            $this->optionManager->need('service.website'));

        $this->baseMailService->sendPlain($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName, $subject, $text, $attachments);
    }

}
