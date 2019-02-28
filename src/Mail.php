<?php

namespace Jawn;

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
     * @param   string|array    $to
     * @param   string          $subject
     * @param   string|array    $message
     * @param   array           $headers
     * @return  bool
     */
    public static function send(
        $to,
        string $subject,
        $message,
        array $headers = []
    ): bool {
        if (is_array($to)) {
            $to = implode(', ', $to);
        }

        if (is_array($message)) {
            $message = implode("\r\n", $message);
        }

        return mail($to, $subject, $message, $headers);
    }
}
