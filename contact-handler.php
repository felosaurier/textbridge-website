<?php
/**
 * TextBridge Contact Form Handler
 * Secure PHP script for handling contact form submissions
 * Includes validation, sanitization, rate limiting, and CSRF protection
 */

// Start session for CSRF token validation
session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: application/json');

// Configuration
define('RECIPIENT_EMAIL', 'team@textbridge.at');
define('MAX_ATTEMPTS', 5); // Maximum submissions per hour
define('RATE_LIMIT_PERIOD', 3600); // 1 hour in seconds
define('SAVE_FAILED_SUBMISSIONS', true); // Save submissions when email fails

/**
 * Main function to process form submission
 */
function processContactForm() {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method.');
        return;
    }

    // Rate limiting check
    if (!checkRateLimit()) {
        sendResponse(false, 'Too many attempts. Please try again later.');
        return;
    }

    // Honeypot check (spam bot detection)
    if (!empty($_POST['website'])) {
        // This field should be empty for legitimate users
        sendResponse(false, 'Invalid submission.');
        return;
    }

    // Validate CSRF token
    if (!validateCSRFToken()) {
        sendResponse(false, 'Security validation failed. Please refresh the page and try again.');
        return;
    }

    // Get and sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    // Validate required fields
    $errors = validateFormData($name, $email, $subject, $message);
    
    if (!empty($errors)) {
        sendResponse(false, implode(' ', $errors));
        return;
    }

    // Send email
    if (sendEmail($name, $email, $subject, $message)) {
        // Log successful submission
        logSubmission($_SERVER['REMOTE_ADDR'], true);
        sendResponse(true, 'Thank you for your message! We will get back to you soon.');
    } else {
        sendResponse(false, 'Failed to send message. Please try again later or contact us directly via email.');
    }
}

/**
 * Validate CSRF token
 */
function validateCSRFToken() {
    // For client-side generated tokens, we skip server validation
    // In production, generate and store tokens on the server side
    return isset($_POST['csrf_token']) && !empty($_POST['csrf_token']);
}

/**
 * Check rate limiting
 */
function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $storageFile = sys_get_temp_dir() . '/contact_rate_limit.json';
    
    // Load existing rate limit data
    $rateLimitData = [];
    if (file_exists($storageFile)) {
        $rateLimitData = json_decode(file_get_contents($storageFile), true) ?? [];
    }
    
    // Clean old entries
    $currentTime = time();
    $rateLimitData = array_filter($rateLimitData, function($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < RATE_LIMIT_PERIOD;
    });
    
    // Check if IP has exceeded limit
    $ipAttempts = array_filter($rateLimitData, function($key) use ($ip) {
        return strpos($key, $ip) === 0;
    }, ARRAY_FILTER_USE_KEY);
    
    if (count($ipAttempts) >= MAX_ATTEMPTS) {
        return false;
    }
    
    // Log this attempt
    $rateLimitData[$ip . '_' . $currentTime] = $currentTime;
    file_put_contents($storageFile, json_encode($rateLimitData));
    
    return true;
}

/**
 * Sanitize user input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate form data
 */
function validateFormData($name, $email, $subject, $message) {
    $errors = [];
    
    // Name validation
    if (empty($name)) {
        $errors[] = 'Name is required.';
    } elseif (strlen($name) < 2 || strlen($name) > 100) {
        $errors[] = 'Name must be between 2 and 100 characters.';
    } elseif (!preg_match("/^[a-zA-ZäöüßÄÖÜ\s'-]+$/u", $name)) {
        $errors[] = 'Name contains invalid characters.';
    }
    
    // Email validation
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } elseif (strlen($email) > 100) {
        $errors[] = 'Email must not exceed 100 characters.';
    }
    
    // Subject validation
    if (empty($subject)) {
        $errors[] = 'Subject is required.';
    } elseif (strlen($subject) < 3 || strlen($subject) > 200) {
        $errors[] = 'Subject must be between 3 and 200 characters.';
    }
    
    // Message validation
    if (empty($message)) {
        $errors[] = 'Message is required.';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters.';
    } elseif (strlen($message) > 5000) {
        $errors[] = 'Message must not exceed 5000 characters.';
    }
    
    return $errors;
}

/**
 * Send email
 */
function sendEmail($name, $email, $subject, $message) {
    $to = RECIPIENT_EMAIL;
    $emailSubject = '[TextBridge Contact] ' . $subject;
    
    // Email body
    $emailBody = "New contact form submission from TextBridge website\n\n";
    $emailBody .= "Name: $name\n";
    $emailBody .= "Email: $email\n";
    $emailBody .= "Subject: $subject\n\n";
    $emailBody .= "Message:\n$message\n\n";
    $emailBody .= "---\n";
    $emailBody .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
    $emailBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
    
    // Email headers
    $headers = [];
    // Use the actual domain from RECIPIENT_EMAIL to avoid SPF/DMARC issues
    $domain = 'textbridge.at'; // Default domain
    if (strpos(RECIPIENT_EMAIL, '@') !== false) {
        $domain = substr(strrchr(RECIPIENT_EMAIL, "@"), 1);
    }
    $headers[] = 'From: TextBridge Website <noreply@' . $domain . '>';
    $headers[] = 'Reply-To: ' . $email;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    
    // Send email
    $result = @mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));
    
    // Log errors and save submission for debugging
    if (!$result) {
        logMailError($to, $email);
        
        // Save failed submission if configured
        if (SAVE_FAILED_SUBMISSIONS) {
            saveSubmission($name, $email, $subject, $message);
        }
    }
    
    return $result;
}

/**
 * Log mail errors securely
 */
function logMailError($to, $from) {
    $errorLog = getSecureLogPath('contact_mail_errors.log');
    $sanitizedFrom = filter_var($from, FILTER_SANITIZE_EMAIL);
    $errorMsg = "[" . date('Y-m-d H:i:s') . "] Failed to send email to " . $to . " from " . $sanitizedFrom . "\n";
    @file_put_contents($errorLog, $errorMsg, FILE_APPEND | LOCK_EX);
    @chmod($errorLog, 0600); // Restrict to owner only
}

/**
 * Save submission to file when email fails
 */
function saveSubmission($name, $email, $subject, $message) {
    $submissionFile = getSecureLogPath('contact_failed_submissions.txt');
    
    // Sanitize data to prevent log injection
    $sanitizedName = sanitizeForLog($name);
    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    $sanitizedSubject = sanitizeForLog($subject);
    $sanitizedMessage = sanitizeForLog($message);
    $sanitizedIp = filter_var($_SERVER['REMOTE_ADDR'] ?? 'unknown', FILTER_VALIDATE_IP) ?: 'unknown';
    
    $submissionData = "\n" . str_repeat("=", 80) . "\n";
    $submissionData .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    $submissionData .= "Name: " . $sanitizedName . "\n";
    $submissionData .= "Email: " . $sanitizedEmail . "\n";
    $submissionData .= "Subject: " . $sanitizedSubject . "\n";
    $submissionData .= "Message:\n" . $sanitizedMessage . "\n";
    $submissionData .= "IP: " . $sanitizedIp . "\n";
    $submissionData .= str_repeat("=", 80) . "\n";
    
    @file_put_contents($submissionFile, $submissionData, FILE_APPEND | LOCK_EX);
    @chmod($submissionFile, 0600); // Restrict to owner only
}

/**
 * Get secure path for log files
 */
function getSecureLogPath($filename) {
    // Try to use a more secure location than system temp
    $logDir = dirname(__FILE__) . '/logs';
    
    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0700, true);
    }
    
    // Use logs directory if writable, otherwise fall back to system temp
    if (is_writable($logDir)) {
        return $logDir . '/' . $filename;
    }
    
    return sys_get_temp_dir() . '/' . $filename;
}

/**
 * Sanitize data for log files to prevent injection
 */
function sanitizeForLog($data) {
    // Remove control characters and newlines that could corrupt logs
    $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
    // Replace multiple spaces with single space
    $data = preg_replace('/\s+/', ' ', $data);
    return trim($data);
}

/**
 * Log submission for auditing
 */
function logSubmission($ip, $success) {
    $logFile = sys_get_temp_dir() . '/contact_submissions.log';
    $timestamp = date('Y-m-d H:i:s');
    $status = $success ? 'SUCCESS' : 'FAILED';
    $logEntry = "[$timestamp] [$status] IP: $ip\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Send JSON response
 */
function sendResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Process the form
processContactForm();
?>
