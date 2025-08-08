<?php
/**
 * Email Service Class
 * 
 * Secure email handling with PHPMailer
 */

require_once __DIR__ . '/../../satei/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../satei/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../satei/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;
    private array $config;
    
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->config = $this->loadEmailConfig();
        $this->configureSMTP();
    }
    
    /**
     * Load email configuration from environment or defaults
     */
    private function loadEmailConfig(): array
    {
        return [
            'smtp_host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'smtp_port' => (int)($_ENV['SMTP_PORT'] ?? 465),
            'smtp_username' => $_ENV['SMTP_USERNAME'] ?? '',
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'from_email' => $_ENV['FROM_EMAIL'] ?? 'noreply@example.com',
            'from_name' => $_ENV['FROM_NAME'] ?? 'Real Estate System',
            'to_email' => $_ENV['TO_EMAIL'] ?? 'admin@example.com',
        ];
    }
    
    /**
     * Configure SMTP settings
     */
    private function configureSMTP(): void
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp_username'];
            $this->mailer->Password = $this->config['smtp_password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port = $this->config['smtp_port'];
            $this->mailer->CharSet = 'UTF-8';
            
            // Security settings
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
        } catch (Exception $e) {
            error_log("SMTP configuration error: " . $e->getMessage());
            throw new Exception("Email service configuration failed.");
        }
    }
    
    /**
     * Send inquiry email
     */
    public function sendInquiry(array $formData): bool
    {
        try {
            // Set sender and recipient
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
            $this->mailer->addAddress($this->config['to_email']);
            $this->mailer->addReplyTo($formData['email'] ?? $this->config['from_email'], $formData['name'] ?? '');
            
            // Email content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'お問い合わせ｜売却査定 - ' . date('Y-m-d H:i:s');
            $this->mailer->Body = $this->generateEmailBody($formData);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate HTML email body
     */
    private function generateEmailBody(array $data): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #004790; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .field { margin-bottom: 15px; padding: 10px; background-color: white; border-left: 4px solid #5DADFF; }
                .field strong { display: inline-block; width: 200px; color: #004790; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>不動産査定お問い合わせ</h1>
                </div>
                <div class="content">
                    <p>新しい査定依頼が届きました。</p>
                    
                    <div class="field">
                        <strong>受信日時:</strong> ' . date('Y年m月d日 H:i:s') . '
                    </div>';
        
        // Add form fields
        $fieldLabels = [
            'property_type' => '物件の種別',
            'location' => '物件の所在地',
            'address_detail' => '詳細住所',
            'mansion_name' => 'マンション名',
            'room_number' => '号室',
            'layout' => '間取り',
            'area' => '専有面積',
            'construction_year' => '築年',
            'current_status' => '現状',
            'relationship' => 'あなたと売却物件との関係',
            'loan_balance' => '住宅ローン残高',
            'desired_price' => '希望買取金額',
            'name' => 'お名前',
            'furigana' => 'フリガナ',
            'gender' => '性別',
            'phone' => '電話番号',
            'contact_time' => 'ご希望の連絡時間帯',
            'email' => 'メールアドレス',
            'contact_method' => '希望する連絡方法',
            'assessment_method' => '希望査定方法'
        ];
        
        foreach ($fieldLabels as $key => $label) {
            if (!empty($data[$key])) {
                $value = is_array($data[$key]) ? implode(', ', $data[$key]) : $data[$key];
                $html .= '<div class="field"><strong>' . $label . ':</strong> ' . htmlspecialchars($value) . '</div>';
            }
        }
        
        $html .= '
                </div>
                <div class="footer">
                    <p>このメールは自動送信されています。</p>
                    <p>&copy; ' . date('Y') . ' Real Estate System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}