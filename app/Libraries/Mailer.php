<?php
namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Konfigurasi dari .env
     */
    protected function configure(): void
    {
        $config = config('Email');

        try {
            // Server SMTP
            $this->mail->isSMTP();
            $this->mail->Host       = $config->SMTPHost;
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $config->SMTPUser;
            $this->mail->Password   = $config->SMTPPass;
            $this->mail->SMTPSecure = $config->SMTPCrypto === 'tls'
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port       = $config->SMTPPort;

            // Karakter
            $this->mail->CharSet = 'UTF-8';

            // Pengirim
            $this->mail->setFrom($config->fromEmail, $config->fromName);

            // HTML mode
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            log_message('error', '[Mailer] Config error: ' . $e->getMessage());
        }
    }

    /**
     * Kirim 1 email.
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            // Penerima
            $this->mail->addAddress($to);

            // CC / BCC
            if (!empty($options['cc'])) {
                foreach ((array) $options['cc'] as $cc) $this->mail->addCC($cc);
            }
            if (!empty($options['bcc'])) {
                foreach ((array) $options['bcc'] as $bcc) $this->mail->addBCC($bcc);
            }
            if (!empty($options['reply_to'])) {
                $this->mail->addReplyTo($options['reply_to']);
            }

            // Attachment
            if (!empty($options['attachments'])) {
                foreach ($options['attachments'] as $file) {
                    if (is_file($file)) {
                        $this->mail->addAttachment($file);
                    }
                }
            }

            // Konten
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);

            $this->mail->send();
            return true;

        } catch (Exception $e) {
            log_message('error', '[Mailer] Gagal kirim ke ' . $to . ': ' . $this->mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Kirim ke banyak penerima (loop send).
     */
    public function sendBulk(array $recipients, string $subject, string $body, array $options = []): int
    {
        $sent = 0;
        foreach ($recipients as $to) {
            if ($this->send($to, $subject, $body, $options)) $sent++;
        }
        return $sent;
    }

    /**
     * Ambil semua email admin & super_admin yang aktif.
     */
    public static function getAdminRecipients(): array
    {
        $rows = model('UserModel')
            ->select('email')
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'aktif')
            ->findAll();
        return array_column($rows, 'email');
    }
}