<?php
/**
 * Form Validation Class
 * 
 * Handles secure form validation with comprehensive sanitization
 */

class FormValidator
{
    private array $errors = [];
    private array $data = [];
    
    public function __construct(array $data)
    {
        $this->data = $this->sanitizeInput($data);
    }
    
    /**
     * Sanitize input data
     */
    private function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove null bytes and trim whitespace
                $value = str_replace("\0", "", $value);
                $value = trim($value);
                // HTML encode for security
                $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Validate required fields
     */
    public function validateRequired(array $requiredFields): self
    {
        foreach ($requiredFields as $field) {
            if (empty($this->data[$field])) {
                $this->errors[$field] = "Field '{$field}' is required.";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate email format
     */
    public function validateEmail(string $field): self
    {
        if (!empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = "Invalid email format.";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate phone number (Japanese format)
     */
    public function validatePhone(string $field): self
    {
        if (!empty($this->data[$field])) {
            $phone = preg_replace('/[^0-9]/', '', $this->data[$field]);
            if (strlen($phone) < 10 || strlen($phone) > 11) {
                $this->errors[$field] = "Invalid phone number format.";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate string length
     */
    public function validateLength(string $field, int $min = 0, int $max = 255): self
    {
        if (!empty($this->data[$field])) {
            $length = mb_strlen($this->data[$field], 'UTF-8');
            if ($length < $min || $length > $max) {
                $this->errors[$field] = "Field length must be between {$min} and {$max} characters.";
            }
        }
        
        return $this;
    }
    
    /**
     * Validate against allowed values
     */
    public function validateInArray(string $field, array $allowedValues): self
    {
        if (!empty($this->data[$field])) {
            if (!in_array($this->data[$field], $allowedValues, true)) {
                $this->errors[$field] = "Invalid value selected.";
            }
        }
        
        return $this;
    }
    
    /**
     * Check if validation passed
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get sanitized data
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    /**
     * Get specific field value
     */
    public function get(string $field, $default = null)
    {
        return $this->data[$field] ?? $default;
    }
}