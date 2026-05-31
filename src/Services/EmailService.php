<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

final class EmailService
{
    private ?PHPMailer $mailer = null;

    private function getMailer(): PHPMailer
    {
        if ($this->mailer === null) {
            $this->mailer = new PHPMailer(true);
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['SMTP_HOST'] ?? '';
            $this->mailer->Port = (int)($_ENV['SMTP_PORT'] ?? 587);
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['SMTP_USER'] ?? '';
            $this->mailer->Password = $_ENV['SMTP_PASS'] ?? '';
            $this->mailer->SMTPSecure = ($this->mailer->Port === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->setFrom($this->mailer->Username, 'Contactformulier');
            $this->mailer->CharSet = 'UTF-8';
        }
        return $this->mailer;
    }

    public function sendContactNotification(array $submission): void
    {
        $subjectLabels = [
            'offerte' => 'Offerte aanvraag',
            'vraag' => 'Algemene vraag',
            'anders' => 'Anders',
        ];

        $label = $subjectLabels[$submission['subject']] ?? $submission['subject'];
        $notificationEmail = $_ENV['NOTIFICATION_EMAIL'] ?: 'info@sebastiaanpeters.nl';

        $html = sprintf(
            '<h2>Nieuw contactformulier bericht</h2>
            <table>
                <tr><td><strong>Naam:</strong></td><td>%s</td></tr>
                <tr><td><strong>E-mail:</strong></td><td>%s</td></tr>
                <tr><td><strong>Onderwerp:</strong></td><td>%s</td></tr>
            </table>
            <h3>Bericht:</h3>
            <p>%s</p>',
            escapeHtml($submission['name']),
            escapeHtml($submission['email']),
            escapeHtml($label),
            nl2br(escapeHtml($submission['message']))
        );

        $mailer = $this->getMailer();
        $mailer->addAddress($notificationEmail);
        $mailer->addReplyTo($submission['email'], $submission['name']);
        $mailer->Subject = sprintf('Nieuwe %s van %s', $label, $submission['name']);
        $mailer->isHTML(true);
        $mailer->Body = $html;
        $mailer->send();
    }
}
