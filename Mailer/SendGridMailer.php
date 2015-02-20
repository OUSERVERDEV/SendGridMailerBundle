<?php

namespace Theodo\SendGridMailerBundle\Mailer;

use SendGrid as SendGridService;
use SendGrid\Email;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Send emails via the sendGridMailer API
 *
 * @author Reynald Mandel <reynaldm@theodo.fr>
 */
class SendGridMailer
{
    /**
     * @var SendGridService
     */
    protected $sendGridService;

    /**
     * @param string     $sendGridUserLogin    the send grid user's account login
     * @param string     $sendGridUserPassword the send grid user's account password
     * @param Filesystem $filesystem           the filesystem service
     */
    public function __construct(
        $sendGridUserLogin,
        $sendGridUserPassword,
        Filesystem $filesystem
    ) {
        $this->sendGridService = new SendGridService(
            $sendGridUserLogin,
            $sendGridUserPassword,
            ['turn_off_ssl_verification' => true]
        );
        $this->filesystem      = $filesystem;
    }

    /**
     * Send an already fully prepared email
     * Return true if the mail has been effectively sent, false otherwise
     *
     * @param Email $email
     *
     * @return bool
     */
    public function sendEmail(Email $email)
    {
        $response = $this->sendGridService->send($email);

        return 'success' === $response->message;
    }

    /**
     * @param string[] $attachments
     */
    public function removeAttachments(array $attachments)
    {
        foreach ($attachments as $attachment) {
            if ($this->filesystem->exists($attachment)) {
                try {
                    $this->filesystem->remove($attachment);
                } catch (IOException $e) {
                }
            }
        }
    }
}
