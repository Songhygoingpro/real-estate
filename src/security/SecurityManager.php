<?php
/**
 * Security Manager Class
 * 
 * Handles security-related functionality including rate limiting, 
 * CSRF protection, and security logging
 */

class SecurityManager
{
    private mysqli $db;
    private array $config;
    
    public function __construct()
    {
        $this->db = DatabaseConfig::getInstance()->getConnection();
        $this->config = [
            'max_attempts' => 5,
            'lockout_time' => 900, // 15 minutes
            'csrf_lifetime' => 3600, // 1 hour
        ];
    }
    
    /**
     * Check rate limiting for IP address
     */
    public function checkRateLimit(string $ipAddress, string $action = 'form_submit'): bool
    {
        $timeWindow = time() - $this->config['lockout_time'];
        
        // Clean old entries
        $this->cleanOldSecurityLogs($timeWindow);
        
        // Count recent attempts
        $sql = "SELECT COUNT(*) as attempt_count FROM security_logs 
                WHERE ip_address = ? AND event_type = ? AND created_at > FROM_UNIXTIME(?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $ipAddress, $action, $timeWindow);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['attempt_count'] < $this->config['max_attempts'];
    }
    
    /**
     * Log security event
     */
    public function logSecurityEvent(
        string $eventType, 
        string $ipAddress, 
        array $details = [], 
        string $severity = 'low'
    ): void {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $detailsJson = json_encode($details);
        
        $sql = "INSERT INTO security_logs (event_type, ip_address, user_agent, details, severity) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssss", $eventType, $ipAddress, $userAgent, $detailsJson, $severity);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Validate CSRF token
     */
    public function validateCSRFToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Check token age
        if (time() - $_SESSION['csrf_token_time'] > $this->config['csrf_lifetime']) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input to prevent XSS
     */
    public function sanitizeInput(string $input): string
    {
        // Remove null bytes
        $input = str_replace("\0", "", $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // HTML encode
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Validate IP address
     */
    public function isValidIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    
    /**
     * Get client IP address
     */
    public function getClientIP(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if ($this->isValidIP($ip) && !$this->isPrivateIP($ip)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Check if IP is private/local
     */
    private function isPrivateIP(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
    
    /**
     * Clean old security logs
     */
    private function cleanOldSecurityLogs(int $timeWindow): void
    {
        $sql = "DELETE FROM security_logs WHERE created_at < FROM_UNIXTIME(?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $timeWindow);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Check for suspicious patterns in input
     */
    public function detectSuspiciousInput(array $data): array
    {
        $suspiciousPatterns = [
            'sql_injection' => '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION)\b)/i',
            'xss_attempt' => '/<script|javascript:|on\w+\s*=/i',
            'path_traversal' => '/\.\.[\/\\\\]/i',
            'command_injection' => '/[;&|`$(){}]/i'
        ];
        
        $detectedThreats = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $threatType => $pattern) {
                    if (preg_match($pattern, $value)) {
                        $detectedThreats[] = [
                            'field' => $key,
                            'threat_type' => $threatType,
                            'value' => substr($value, 0, 100) // Limit logged value length
                        ];
                    }
                }
            }
        }
        
        return $detectedThreats;
    }
}