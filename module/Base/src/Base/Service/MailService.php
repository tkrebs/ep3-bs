<?php

namespace Base\Service;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class MailService extends AbstractService
{

    protected $mailTransportService;
    protected $mailTransport;

    public function __construct(MailTransportService $mailTransportService)
    {
        $this->mailTransportService = $mailTransportService;
        $this->mailTransport = $mailTransportService->getTransport();
    }

    public function send($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName,
        $subject, array $mimeParts)
    {
        $mail = new Message();

        if ($fromAddress && $fromName) {
            $mail->setFrom($fromAddress, $fromName);
        } else if ($fromAddress) {
            $mail->setFrom($fromAddress);
        }

        if ($replyToAddress && $replyToName) {
            $mail->setReplyTo($replyToAddress, $replyToName);
        } else if ($replyToAddress) {
            $mail->setReplyTo($replyToAddress);
        }

        if ($toAddress && $toName) {
            $mail->setTo($toAddress, $toName);
        } else if ($toAddress) {
            $mail->setTo($toAddress);
        }

        $mail->setSubject($subject);
        $mail->setEncoding('utf-8');

        $mime = new MimeMessage();
        $mime->setParts($mimeParts);

        $mail->setBody($mime);

        $this->mailTransport->send($mail);

        $this->getEventManager()->trigger('send', $mail);
    }

    public function sendWithAttachments($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName,
        $subject, array $mimeParts, array $attachments = array())
    {
        foreach ($attachments as $attachment) {
            $mimePart = new MimePart($attachment['content']);
            $mimePart->type = $attachment['type'];
            $mimePart->encoding = Mime::ENCODING_BASE64;

            if (isset($attachment['disposition'])) {
                $mimePart->disposition = $attachment['disposition'];
            } else {
                $mimePart->disposition = 'attachment';
            }

            $mimePart->filename = $attachment['name'];

            $mimeParts[] = $mimePart;
        }

        $this->send($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName, $subject, $mimeParts);
    }

    public function sendPlain($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName,
        $subject, $text, array $attachments = array())
    {
        $part = new MimePart($text);
        $part->type = 'text/plain';
        $part->charset = 'utf-8';

        $mimeParts = array( $part );

        $this->sendWithAttachments($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName, $subject, $mimeParts, $attachments);
    }

    public function sendHtml($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName,
        $subject, $html, array $attachments = array())
    {
        $part = new MimePart($html);
        $part->type = 'text/html';
        $part->charset = 'utf-8';

        $mimeParts = array( $part );

        $this->sendWithAttachments($fromAddress, $fromName, $replyToAddress, $replyToName, $toAddress, $toName, $subject, $mimeParts, $attachments);
    }

}