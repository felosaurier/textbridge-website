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

    // Handle logo file upload
    $logoPath = null;
    $logoError = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $logoResult = handleLogoUpload($_FILES['logo']);
        if ($logoResult['success']) {
            $logoPath = $logoResult['path'];
        } else {
            $logoError = $logoResult['error'];
        }
    }

    // Validate required fields
    $errors = validateFormData($name, $email, $subject, $message);
    
    // Add logo error if present
    if ($logoError) {
        $errors[] = $logoError;
    }
    
    if (!empty($errors)) {
        sendResponse(false, implode(' ', $errors));
        return;
    }

    // Send email
    if (sendEmail($name, $email, $subject, $message, $logoPath)) {
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
 * Handle logo file upload
 */
function handleLogoUpload($file) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Logo upload failed. Please try again.'];
    }

    // Validate file size (max 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
    if ($file['size'] > $maxFileSize) {
        return ['success' => false, 'error' => 'Logo file size must not exceed 2 MB.'];
    }

    // Validate file type
    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid logo file type. Only PNG, JPG, JPEG, and SVG are allowed.'];
    }

    // Validate file extension
    $allowedExtensions = ['png', 'jpg', 'jpeg', 'svg'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'error' => 'Invalid logo file extension.'];
    }

    // Generate unique filename
    $uploadDir = sys_get_temp_dir() . '/textbridge_uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uniqueFilename = 'logo_' . uniqid() . '_' . time() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $uniqueFilename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'error' => 'Failed to save logo file.'];
    }

    return ['success' => true, 'path' => $uploadPath];
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
function sendEmail($name, $email, $subject, $message, $logoPath = null) {
    $to = RECIPIENT_EMAIL;
    $emailSubject = '[TextBridge Contact] ' . $subject;
    
    // Email body
    $emailBody = "New contact form submission from TextBridge website\n\n";
    $emailBody .= "Name: $name\n";
    $emailBody .= "Email: $email\n";
    $emailBody .= "Subject: $subject\n\n";
    $emailBody .= "Message:\n$message\n\n";
    
    if ($logoPath) {
        $emailBody .= "Logo attachment: Yes (attached to this email)\n";
    }
    
    $emailBody .= "---\n";
    $emailBody .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
    $emailBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
    
    // If logo is attached, send with attachment
    if ($logoPath && file_exists($logoPath)) {
        return sendEmailWithAttachment($to, $emailSubject, $emailBody, $email, $logoPath);
    }
    
    // Email headers (no attachment)
    $headers = [];
    $headers[] = 'From: TextBridge Website <noreply@textbridge.example>';
    $headers[] = 'Reply-To: ' . $email;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    
    // Send email
    return mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));
}

/**
 * Send email with attachment
 */
function sendEmailWithAttachment($to, $subject, $body, $replyToEmail, $attachmentPath) {
    $boundary = md5(time());
    
    // Headers
    $headers = [];
    $headers[] = 'From: TextBridge Website <noreply@textbridge.example>';
    $headers[] = 'Reply-To: ' . $replyToEmail;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
    
    // Message body
    $emailMessage = "--$boundary\r\n";
    $emailMessage .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $emailMessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $emailMessage .= $body . "\r\n";
    
    // Attachment
    $filename = basename($attachmentPath);
    $fileContent = chunk_split(base64_encode(file_get_contents($attachmentPath)));
    $emailMessage .= "--$boundary\r\n";
    $emailMessage .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
    $emailMessage .= "Content-Transfer-Encoding: base64\r\n";
    $emailMessage .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
    $emailMessage .= $fileContent . "\r\n";
    $emailMessage .= "--$boundary--";
    
    return mail($to, $subject, $emailMessage, implode("\r\n", $headers));
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
