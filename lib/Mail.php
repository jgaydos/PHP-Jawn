<?php

/**
 * Send mail
 */
class Mail
{
    /**
     * Send mail
     * $headers = 'From: webmaster@example.com' . "\r\n" .
     *     'Reply-To: webmaster@example.com' . "\r\n" .
     *     'X-Mailer: PHP/' . phpversion();
     */
    public static function send(
        mixed $to,
        string $subject,
        mixed $message,
        array $headers = []
    ): bool {
        Console::info('Sendig mail');
        if (is_array($to)) {
            $to = implode(', ', $to);
        }

        if (is_array($message)) {
            $message = implode("\r\n", $message);
        }

        return mail($to, $subject, $message, $headers);
    }
}
