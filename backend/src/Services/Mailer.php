<?php
declare(strict_types=1);

namespace DriveMaster\Services;

/**
 * DriveMaster Pure PHP SMTP Mailer Service
 * Compliant with RFC 5321 (SMTP), RFC 2045 (MIME), and RFC 3207 (STARTTLS)
 */
class Mailer
{
    private array $config;

    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = $config;
        } else {
            $configFile = __DIR__ . '/../../config/smtp.php';
            $this->config = file_exists($configFile) ? require $configFile : [];
        }
    }

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Send an email via SMTP
     *
     * @param string $to Recipient email address
     * @param string $subject Subject line
     * @param string $htmlBody HTML content
     * @param string|null $recipientName Optional recipient name
     * @return array [success => bool, log => array, error => ?string]
     */
    public function send(string $to, string $subject, string $htmlBody, ?string $recipientName = null): array
    {
        $host       = $this->config['host'] ?? '127.0.0.1';
        $port       = (int)($this->config['port'] ?? 587);
        $encryption = strtolower((string)($this->config['encryption'] ?? 'tls'));
        $timeout    = (int)($this->config['timeout'] ?? 8);
        $fromEmail  = $this->config['from_email'] ?? 'notifications@drivemaster-school.com';
        $fromName   = $this->config['from_name'] ?? 'DriveMaster Driving Academy';
        $username   = $this->config['username'] ?? '';
        $password   = $this->config['password'] ?? '';
        $auth       = (bool)($this->config['auth'] ?? true);

        $logEntry = [
            'id'             => uniqid('mail_', true),
            'timestamp'      => date('Y-m-d H:i:s'),
            'to'             => $to,
            'recipient_name' => $recipientName,
            'from'           => "{$fromName} <{$fromEmail}>",
            'subject'        => $subject,
            'smtp_host'      => "{$host}:{$port} ({$encryption})",
            'status'         => 'pending',
            'error'          => null,
            'body_snippet'   => substr(strip_tags($htmlBody), 0, 160) . '...'
        ];

        $smtpSuccess = false;
        $errorMsg = null;

        try {
            // Determine protocol prefix
            $protocol = ($encryption === 'ssl') ? 'ssl://' : '';
            $connectAddress = $protocol . $host . ':' . $port;

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]);

            $socket = @stream_socket_client(
                $connectAddress,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                throw new \Exception("Cannot connect to SMTP server {$connectAddress}: {$errstr} ({$errno})");
            }

            stream_set_timeout($socket, $timeout);

            // Read greeting (220)
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '220')) {
                throw new \Exception("Invalid SMTP greeting: {$response}");
            }

            // EHLO
            $clientHost = gethostname() ?: 'localhost';
            $this->sendCommand($socket, "EHLO {$clientHost}");
            $response = $this->readResponse($socket);

            // STARTTLS negotiation if encryption is TLS
            if ($encryption === 'tls') {
                $this->sendCommand($socket, "STARTTLS");
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '220')) {
                    throw new \Exception("STARTTLS rejected: {$response}");
                }

                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                }

                $cryptoOk = @stream_socket_enable_crypto($socket, true, $cryptoMethod);
                if (!$cryptoOk) {
                    throw new \Exception("TLS handshake negotiation failed with {$host}");
                }

                // Resend EHLO over TLS
                $this->sendCommand($socket, "EHLO {$clientHost}");
                $response = $this->readResponse($socket);
            }

            // Authentication
            if ($auth && !empty($username)) {
                $this->sendCommand($socket, "AUTH LOGIN");
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '334')) {
                    throw new \Exception("AUTH LOGIN rejected: {$response}");
                }

                $this->sendCommand($socket, base64_encode($username));
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '334')) {
                    throw new \Exception("Username rejected: {$response}");
                }

                $this->sendCommand($socket, base64_encode($password));
                $response = $this->readResponse($socket);
                if (!str_starts_with($response, '235')) {
                    throw new \Exception("Authentication credentials failed: {$response}");
                }
            }

            // MAIL FROM
            $this->sendCommand($socket, "MAIL FROM:<{$fromEmail}>");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '250')) {
                throw new \Exception("MAIL FROM failed: {$response}");
            }

            // RCPT TO
            $this->sendCommand($socket, "RCPT TO:<{$to}>");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '250') && !str_starts_with($response, '251')) {
                throw new \Exception("RCPT TO failed for {$to}: {$response}");
            }

            // DATA
            $this->sendCommand($socket, "DATA");
            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '354')) {
                throw new \Exception("DATA command rejected: {$response}");
            }

            // Build Headers and MIME payload
            $msgId = '<' . time() . '.' . bin2hex(random_bytes(6)) . '@' . $clientHost . '>';
            $date  = date('r');
            $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $toHeader = !empty($recipientName) ? "=?UTF-8?B?" . base64_encode($recipientName) . "?= <{$to}>" : $to;
            $fromHeader = "=?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>";

            $headers = [
                "Date: {$date}",
                "From: {$fromHeader}",
                "To: {$toHeader}",
                "Subject: {$encodedSubject}",
                "Message-ID: {$msgId}",
                "X-Mailer: DriveMaster-PHP-SMTP/1.0",
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=UTF-8",
                "Content-Transfer-Encoding: 8bit"
            ];

            // Normalize line endings in body
            $body = str_replace(["\r\n", "\r"], "\n", $htmlBody);
            $body = str_replace("\n", "\r\n", $body);

            // Dot-stuffing (RFC 5321: lines starting with dot must be doubled)
            $body = preg_replace('/^\./m', '..', $body);

            $payload = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
            fwrite($socket, $payload);

            $response = $this->readResponse($socket);
            if (!str_starts_with($response, '250')) {
                throw new \Exception("Message body transmission rejected: {$response}");
            }

            // QUIT
            $this->sendCommand($socket, "QUIT");
            @fclose($socket);

            $smtpSuccess = true;
            $logEntry['status'] = 'delivered_via_smtp';
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            $logEntry['status'] = 'smtp_fallback_logged';
            $logEntry['error'] = $errorMsg;
            if (isset($socket) && is_resource($socket)) {
                @fclose($socket);
            }
        }

        // Persist to mail log
        self::saveMailLog($logEntry, $htmlBody);

        return [
            'success'     => $smtpSuccess,
            'delivered'   => $smtpSuccess,
            'logged'      => true,
            'log_id'      => $logEntry['id'],
            'status'      => $logEntry['status'],
            'recipient'   => $to,
            'subject'     => $subject,
            'error'       => $errorMsg
        ];
    }

    private function sendCommand($socket, string $cmd): void
    {
        fwrite($socket, $cmd . "\r\n");
    }

    private function readResponse($socket): string
    {
        $data = '';
        while (!feof($socket)) {
            $line = fgets($socket, 512);
            if ($line === false) break;
            $data .= $line;
            // End of multiline response (e.g. '250 ' instead of '250-')
            if (preg_match('/^[0-9]{3} /', $line)) {
                break;
            }
        }
        return trim($data);
    }

    private static function saveMailLog(array $entry, string $fullBody): void
    {
        $logFile = __DIR__ . '/../../../database/mail_log.json';
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $entry['html_body'] = $fullBody;
        $logs = [];
        if (file_exists($logFile)) {
            $raw = file_get_contents($logFile);
            $parsed = json_decode($raw, true);
            if (is_array($parsed)) {
                $logs = $parsed;
            }
        }

        array_unshift($logs, $entry);
        if (count($logs) > 200) {
            $logs = array_slice($logs, 0, 200);
        }

        file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public static function getMailLogs(): array
    {
        $logFile = __DIR__ . '/../../../database/mail_log.json';
        if (!file_exists($logFile)) return [];
        $raw = file_get_contents($logFile);
        $parsed = json_decode($raw, true);
        return is_array($parsed) ? $parsed : [];
    }

    // =========================================================================
    // HIGH LEVEL EMAIL NOTIFICATIONS (SQUARE SANDBOX & APPROVAL WORKFLOW)
    // =========================================================================

    /**
     * 1. Send Notification to Admin when User requests/initiates payment
     */
    public function notifyAdminNewPaymentRequest(array $enrollment): array
    {
        $adminEmail = $this->config['admin_email'] ?? 'admin@drivemaster-school.com';
        $ref        = $enrollment['booking_reference'] ?? 'N/A';
        $student    = $enrollment['student_name'] ?? 'Student';
        $email      = $enrollment['student_email'] ?? 'N/A';
        $phone      = $enrollment['student_phone'] ?? 'N/A';
        $course     = $enrollment['course_name'] ?? 'Driving Course';
        $price      = number_format((float)($enrollment['course_price'] ?? 0), 2);
        $appUrl     = $this->config['url'] ?? 'http://localhost:8000';
        $adminUrl   = $appUrl . '/admin/enrollments.php';

        $subject = "🚗 [ACTION REQUIRED] New Square Payment Request: {$ref} - {$student}";

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 20px; color: #1e293b; }
    .box { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    .header { background: #133a30; padding: 24px 30px; color: #ffffff; }
    .header h2 { margin: 0; font-size: 20px; font-weight: 700; }
    .badge { display: inline-block; background: #eab308; color: #000000; font-weight: bold; font-size: 11px; padding: 4px 8px; border-radius: 4px; margin-top: 6px; }
    .body { padding: 30px; line-height: 1.6; }
    .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #f8fafc; border-radius: 8px; overflow: hidden; }
    .detail-table td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
    .detail-table td.label { font-weight: 600; color: #64748b; width: 38%; }
    .detail-table td.value { font-weight: 700; color: #0f172a; }
    .price-tag { color: #00b060; font-size: 18px; font-weight: 800; }
    .btn { display: inline-block; background: #00b060; color: #ffffff !important; text-decoration: none; font-weight: bold; font-size: 14px; padding: 12px 24px; border-radius: 8px; margin-top: 15px; }
    .footer { background: #f8fafc; padding: 16px 30px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; }
  </style>
</head>
<body>
  <div class="box">
    <div class="header">
      <h2>DriveMaster Administration</h2>
      <span class="badge">AWAITING ADMIN APPROVAL</span>
    </div>
    <div class="body">
      <p style="font-size: 16px; margin-top: 0;"><strong>Hello Administrator,</strong></p>
      <p>A student has just initiated a <strong>Square Test Sandbox Payment</strong> request for a driving course. Please review the booking details and grant approval in the admin panel so the student can complete their checkout.</p>
      
      <table class="detail-table">
        <tr>
          <td class="label">Booking Reference:</td>
          <td class="value"><span style="font-family: monospace; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{$ref}</span></td>
        </tr>
        <tr>
          <td class="label">Student Name:</td>
          <td class="value">{$student}</td>
        </tr>
        <tr>
          <td class="label">Student Email:</td>
          <td class="value"><a href="mailto:{$email}">{$email}</a></td>
        </tr>
        <tr>
          <td class="label">Student Phone:</td>
          <td class="value">{$phone}</td>
        </tr>
        <tr>
          <td class="label">Course Package:</td>
          <td class="value">{$course}</td>
        </tr>
        <tr>
          <td class="label">Amount Payable:</td>
          <td class="value"><span class="price-tag">\${$price}</span></td>
        </tr>
        <tr>
          <td class="label">Payment Gateway:</td>
          <td class="value">Square Test Sandbox (2-Step Approval)</td>
        </tr>
        <tr>
          <td class="label">Current Status:</td>
          <td class="value"><span style="color: #d97706;">Payment Requested</span></td>
        </tr>
      </table>

      <div style="text-align: center; margin: 25px 0;">
        <a href="{$adminUrl}" class="btn">Open Admin Panel to Approve Request &rarr;</a>
      </div>

      <p style="font-size: 12px; color: #64748b;">As soon as you click <strong>Approve Payment</strong>, an automated approval email will be sent to the student via SMTP, and their Square payment form will unlock automatically.</p>
    </div>
    <div class="footer">
      Sent automatically by DriveMaster SMTP Mailer System &bull; Square Sandbox Mode
    </div>
  </div>
</body>
</html>
HTML;

        return $this->send($adminEmail, $subject, $html, 'DriveMaster Administrator');
    }

    /**
     * 2. Send Notification to User when Admin APPROVES their payment request
     */
    public function notifyUserPaymentApproved(array $enrollment): array
    {
        $userEmail  = $enrollment['student_email'] ?? '';
        if (empty($userEmail)) {
            return ['success' => false, 'error' => 'No student email provided.'];
        }

        $ref        = $enrollment['booking_reference'] ?? 'N/A';
        $student    = $enrollment['student_name'] ?? 'Valued Student';
        $course     = $enrollment['course_name'] ?? 'Driving Course';
        $price      = number_format((float)($enrollment['course_price'] ?? 0), 2);
        $appUrl     = $this->config['url'] ?? 'http://localhost:8000';
        $coursesUrl = $appUrl . '/courses';

        $subject = "✅ Payment Request Approved! Complete Your Booking - {$ref}";

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 20px; color: #1e293b; }
    .box { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    .header { background: #00b060; padding: 24px 30px; color: #ffffff; }
    .header h2 { margin: 0; font-size: 20px; font-weight: 700; }
    .badge { display: inline-block; background: #ffffff; color: #00b060; font-weight: bold; font-size: 11px; padding: 4px 8px; border-radius: 4px; margin-top: 6px; }
    .body { padding: 30px; line-height: 1.6; }
    .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #f8fafc; border-radius: 8px; overflow: hidden; }
    .detail-table td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
    .detail-table td.label { font-weight: 600; color: #64748b; width: 38%; }
    .detail-table td.value { font-weight: 700; color: #0f172a; }
    .price-tag { color: #00b060; font-size: 18px; font-weight: 800; }
    .btn { display: inline-block; background: #111111; color: #ffffff !important; text-decoration: none; font-weight: bold; font-size: 14px; padding: 12px 24px; border-radius: 8px; margin-top: 15px; }
    .sandbox-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px; margin: 15px 0; font-size: 13px; color: #166534; }
    .footer { background: #f8fafc; padding: 16px 30px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; }
  </style>
</head>
<body>
  <div class="box">
    <div class="header">
      <h2>DriveMaster Driving Academy</h2>
      <span class="badge">PAYMENT AUTHORIZED</span>
    </div>
    <div class="body">
      <p style="font-size: 16px; margin-top: 0;"><strong>Dear {$student},</strong></p>
      <p>Great news! Your driving course payment request has been <strong>approved</strong> by our administration team. You are now authorized to complete your Square payment.</p>
      
      <table class="detail-table">
        <tr>
          <td class="label">Booking Reference:</td>
          <td class="value"><span style="font-family: monospace; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{$ref}</span></td>
        </tr>
        <tr>
          <td class="label">Selected Package:</td>
          <td class="value">{$course}</td>
        </tr>
        <tr>
          <td class="label">Authorized Amount:</td>
          <td class="value"><span class="price-tag">\${$price}</span></td>
        </tr>
        <tr>
          <td class="label">Payment Gateway:</td>
          <td class="value">Square Test Sandbox</td>
        </tr>
        <tr>
          <td class="label">Status:</td>
          <td class="value"><span style="color: #00b060;">Approved to Pay</span></td>
        </tr>
      </table>

      <div class="sandbox-box">
        <strong>🧪 Square Sandbox Test Card:</strong><br>
        Card Number: <code>4242 4242 4242 4242</code> &bull; Exp: <code>12/28</code> &bull; CVV: <code>123</code> &bull; Zip: <code>90210</code>
      </div>

      <div style="text-align: center; margin: 25px 0;">
        <a href="{$coursesUrl}" class="btn">Complete Square Checkout Now &rarr;</a>
      </div>

      <p style="font-size: 13px; color: #64748b;">If you already have the checkout window open on our website, it will automatically unlock for you. Once paid, your instructor and schedule will be confirmed immediately.</p>
    </div>
    <div class="footer">
      DriveMaster Academy &bull; 124 Highway Central Blvd, City Driving District &bull; Phone: +66 12 000 3456
    </div>
  </div>
</body>
</html>
HTML;

        return $this->send($userEmail, $subject, $html, $student);
    }

    /**
     * 3. Send Payment Successful Receipt to User once payment is confirmed
     */
    public function notifyUserPaymentSuccessReceipt(array $enrollment): array
    {
        $userEmail  = $enrollment['student_email'] ?? '';
        if (empty($userEmail)) {
            return ['success' => false, 'error' => 'No student email provided.'];
        }

        $ref        = $enrollment['booking_reference'] ?? 'N/A';
        $student    = $enrollment['student_name'] ?? 'Valued Student';
        $course     = $enrollment['course_name'] ?? 'Driving Course';
        $price      = number_format((float)($enrollment['course_price'] ?? 0), 2);
        $txId       = $enrollment['payment_tx_id'] ?? ('sq_sandbox_tx_' . bin2hex(random_bytes(6)));
        $last4      = $enrollment['card_last4'] ?? '4242';
        $cardBrand  = $enrollment['card_brand'] ?? 'Square Sandbox Visa';
        $paidAt     = $enrollment['paid_at'] ?? date('Y-m-d H:i:s');

        $subject = "🎉 Payment Successful! Driving Course Confirmed - {$ref}";

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 20px; color: #1e293b; }
    .box { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    .header { background: #0f172a; padding: 24px 30px; color: #ffffff; text-align: center; }
    .header h2 { margin: 0; font-size: 22px; font-weight: 700; color: #10b981; }
    .body { padding: 30px; line-height: 1.6; }
    .receipt-box { border: 2px dashed #cbd5e1; border-radius: 8px; padding: 18px; margin: 20px 0; background: #f8fafc; }
    .receipt-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
    .receipt-row.total { border-top: 1px solid #cbd5e1; padding-top: 10px; margin-top: 10px; font-size: 16px; font-weight: bold; }
    .detail-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    .detail-table td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .footer { background: #f8fafc; padding: 16px 30px; font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; }
  </style>
</head>
<body>
  <div class="box">
    <div class="header">
      <h2>✓ Official Payment Confirmation</h2>
      <p style="margin: 5px 0 0; color: #94a3b8; font-size: 13px;">DriveMaster Driving School &bull; Square Sandbox</p>
    </div>
    <div class="body">
      <p style="font-size: 16px; margin-top: 0;"><strong>Dear {$student},</strong></p>
      <p>Thank you for choosing DriveMaster Driving School! Your payment of <strong>\${$price}</strong> has been successfully authorized and your driving training is officially <strong>confirmed and paid in full</strong>.</p>
      
      <div class="receipt-box">
        <table style="width: 100%; border-collapse: collapse;">
          <tr>
            <td style="color: #64748b; font-size: 13px; padding: 4px 0;">Square Sandbox Transaction ID:</td>
            <td style="text-align: right; font-family: monospace; font-weight: bold; font-size: 13px;">{$txId}</td>
          </tr>
          <tr>
            <td style="color: #64748b; font-size: 13px; padding: 4px 0;">Booking Reference:</td>
            <td style="text-align: right; font-family: monospace; font-weight: bold; font-size: 13px;">{$ref}</td>
          </tr>
          <tr>
            <td style="color: #64748b; font-size: 13px; padding: 4px 0;">Package Enrolled:</td>
            <td style="text-align: right; font-weight: bold; font-size: 13px;">{$course}</td>
          </tr>
          <tr>
            <td style="color: #64748b; font-size: 13px; padding: 4px 0;">Payment Method:</td>
            <td style="text-align: right; font-size: 13px;">{$cardBrand} (&bull;&bull;&bull;&bull; {$last4})</td>
          </tr>
          <tr>
            <td style="color: #64748b; font-size: 13px; padding: 4px 0;">Payment Date:</td>
            <td style="text-align: right; font-size: 13px;">{$paidAt}</td>
          </tr>
          <tr>
            <td colspan="2" style="border-top: 1px solid #cbd5e1; padding-top: 10px; margin-top: 10px;"></td>
          </tr>
          <tr>
            <td style="font-size: 16px; font-weight: bold; color: #0f172a;">Total Paid in Full:</td>
            <td style="text-align: right; font-size: 18px; font-weight: 800; color: #00b060;">\${$price}</td>
          </tr>
        </table>
      </div>

      <h4 style="margin: 20px 0 8px; color: #0f172a;">What happens next?</h4>
      <ul style="color: #475569; font-size: 14px; padding-left: 20px; line-height: 1.7;">
        <li>Your dedicated DVSA Grade-A instructor has been assigned.</li>
        <li>Our student operations desk will call you within 24 hours to schedule your pickup time and location.</li>
        <li>Dual-control hybrid vehicle and free theory app access are reserved under your reference <strong>{$ref}</strong>.</li>
      </ul>

      <p style="font-size: 13px; color: #64748b; margin-top: 25px;">Need immediate support? Call our 24/7 student booking line at <strong>+66 12 000 3456</strong>.</p>
    </div>
    <div class="footer">
      DriveMaster Driving Academy &bull; Square Sandbox Electronic Receipt &bull; All Rights Reserved
    </div>
  </div>
</body>
</html>
HTML;

        return $this->send($userEmail, $subject, $html, $student);
    }
}
