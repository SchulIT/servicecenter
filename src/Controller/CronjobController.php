<?php

namespace App\Controller;

use SchoolIT\CommonBundle\Controller\AbstractCronjobController;
use Symfony\Component\Routing\Annotation\Route;

class CronjobController extends AbstractCronjobController {

    /**
     * @Route("/cron/mails/send")
     */
    public function sendMails() {
        $this->denyAccessUnlessGranted('ROLE_CRON');

        return $this->runCommand([
            'command' => 'swiftmailer:spool:send',
            '--message-limit' => $this->getParameter('email_message_limit')
        ]);
    }

    /**
     * @Route("/cron/uploads/cleanup")
     */
    public function cleanupImages() {
        $this->denyAccessUnlessGranted('ROLE_CRON');

        return $this->runCommand([
            'command' => 'app:uploads:cleanup'
        ]);
    }
}