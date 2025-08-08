<?php
/**
 * Inquiry Handler Class
 * 
 * Handles form processing with security and validation
 */

require_once __DIR__ . '/FormValidator.php';
require_once __DIR__ . '/EmailService.php';
require_once __DIR__ . '/../../config/database.php';

class InquiryHandler
{
    private FormValidator $validator;
    private EmailService $emailService;
    private mysqli $db;
    
    public function __construct()
    {
        $this->emailService = new EmailService();
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }
    
    /**
     * Process form submission
     */
    public function processInquiry(array $postData, array $sessionData = []): array
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Merge POST data with session data
        $formData = array_merge($sessionData, $postData);
        
        // Initialize validator
        $this->validator = new FormValidator($formData);
        
        // Validate form data
        if (!$this->validateForm()) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'フォームの入力内容に問題があります。'
            ];
        }
        
        $sanitizedData = $this->validator->getData();
        
        try {
            // Save to database
            $inquiryId = $this->saveToDatabase($sanitizedData);
            
            // Send email
            $emailSent = $this->emailService->sendInquiry($sanitizedData);
            
            if ($emailSent) {
                // Clear session data on success
                $this->clearSessionData();
                
                return [
                    'success' => true,
                    'inquiry_id' => $inquiryId,
                    'message' => 'お問い合わせを受け付けました。'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'メール送信に失敗しました。しばらく後にもう一度お試しください。'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Inquiry processing error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'システムエラーが発生しました。しばらく後にもう一度お試しください。'
            ];
        }
    }
    
    /**
     * Validate form data
     */
    private function validateForm(): bool
    {
        $requiredFields = [
            'property_type', 'prefecture', 'city', 'town',
            'construction_year', 'current_status', 'relationship',
            'name', 'furigana', 'gender', 'phone', 'email'
        ];
        
        $propertyTypes = ['マンション', '一戸建て', '土地'];
        $genders = ['男性', '女性', '回答しない'];
        $currentStatuses = ['ご自身またはご家族・親戚が居住中', '賃貸中', '空き家'];
        
        $this->validator
            ->validateRequired($requiredFields)
            ->validateEmail('email')
            ->validatePhone('phone')
            ->validateLength('name', 1, 100)
            ->validateLength('furigana', 1, 100)
            ->validateInArray('property_type', $propertyTypes)
            ->validateInArray('gender', $genders)
            ->validateInArray('current_status', $currentStatuses);
        
        return $this->validator->isValid();
    }
    
    /**
     * Save inquiry to database
     */
    private function saveToDatabase(array $data): int
    {
        $sql = "INSERT INTO inquiries (
            property_type, prefecture, city, town, address_detail,
            mansion_name, room_number, layout, area, construction_year,
            current_status, relationship, loan_balance, desired_price,
            name, furigana, gender, phone, contact_time, email,
            contact_method, assessment_method, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $this->db->error);
        }
        
        // Prepare data for binding
        $contactMethod = $this->combineArrayFields([
            $data['contact_method_phone'] ?? '',
            $data['contact_method_email'] ?? ''
        ]);
        
        $assessmentMethod = $this->combineArrayFields([
            $data['assessment_method_desk'] ?? '',
            $data['assessment_method_visit'] ?? ''
        ]);
        
        $location = trim(($data['prefecture'] ?? '') . ($data['city'] ?? '') . ($data['town'] ?? ''));
        $addressDetail = trim(($data['address_detail'] ?? '') . ' ' . ($data['mansion_name'] ?? '') . ' ' . ($data['room_number'] ?? ''));
        
        $stmt->bind_param(
            "sssssssssssssssssssssss",
            $data['property_type'],
            $data['prefecture'],
            $data['city'],
            $data['town'],
            $addressDetail,
            $data['mansion_name'] ?? '',
            $data['room_number'] ?? '',
            $data['layout'] ?? '',
            $data['area'] ?? '',
            $data['construction_year'],
            $data['current_status'],
            $data['relationship'],
            $data['loan_balance'] ?? '',
            $data['desired_price'] ?? '',
            $data['name'],
            $data['furigana'],
            $data['gender'],
            $data['phone'],
            $data['contact_time'] ?? '',
            $data['email'],
            $contactMethod,
            $assessmentMethod
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Database execution failed: " . $stmt->error);
        }
        
        $inquiryId = $this->db->insert_id;
        $stmt->close();
        
        return $inquiryId;
    }
    
    /**
     * Combine array fields with separator
     */
    private function combineArrayFields(array $fields, string $separator = ' / '): string
    {
        $filtered = array_filter($fields, function($value) {
            return !empty($value);
        });
        
        return implode($separator, $filtered);
    }
    
    /**
     * Clear session data
     */
    private function clearSessionData(): void
    {
        $sessionKeys = ['物件の種別', 'prefecture', 'city', 'town'];
        
        foreach ($sessionKeys as $key) {
            unset($_SESSION[$key]);
        }
    }
}