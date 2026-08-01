<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Helper
{
    /**
     * Generate a unique SKU
     */
    public static function generateSku($prefix = 'PRD', $model = null)
    {
        $random = strtoupper(Str::random(8));
        $sku = $prefix . '-' . $random;

        if ($model) {
            while ($model::where('sku', $sku)->exists()) {
                $random = strtoupper(Str::random(8));
                $sku = $prefix . '-' . $random;
            }
        }

        return $sku;
    }

    /**
     * Generate a unique barcode
     */
    public static function generateBarcode($model = null)
    {
        $barcode = 'BC' . rand(10000000, 99999999);

        if ($model) {
            while ($model::where('barcode', $barcode)->exists()) {
                $barcode = 'BC' . rand(10000000, 99999999);
            }
        }

        return $barcode;
    }

    /**
     * Generate a unique invoice number
     */
    public static function generateInvoiceNumber($prefix = 'INV', $model = null)
    {
        $year = date('Y');
        $month = date('m');
        
        if ($model) {
            $lastInvoice = $model::orderBy('id', 'desc')->first();
            $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        } else {
            $number = 1;
        }

        return $prefix . '-' . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Format currency
     */
    public static function formatCurrency($amount, $currency = null)
    {
        $currency = $currency ?? Setting::where('key', 'currency')->first()->value ?? 'PKR';
        return $currency . ' ' . number_format($amount, 2);
    }

    /**
     * Get setting value
     */
    public static function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Upload file
     */
    public static function uploadFile($file, $path, $oldFile = null)
    {
        if ($oldFile) {
            Storage::delete('public/' . $oldFile);
        }

        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $filename, 'public');
    }

    /**
     * Delete file
     */
    public static function deleteFile($path)
    {
        if ($path && Storage::exists('public/' . $path)) {
            Storage::delete('public/' . $path);
            return true;
        }
        return false;
    }

    /**
     * Get file URL
     */
    public static function getFileUrl($path)
    {
        if ($path) {
            return Storage::url($path);
        }
        return null;
    }

    /**
     * Generate random string
     */
    public static function randomString($length = 10)
    {
        return Str::random($length);
    }

    /**
     * Generate slug
     */
    public static function generateSlug($string, $model = null, $field = 'slug')
    {
        $slug = Str::slug($string);
        
        if ($model) {
            $count = 2;
            $originalSlug = $slug;
            
            while ($model::where($field, $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        }
        
        return $slug;
    }

    /**
     * Calculate percentage
     */
    public static function calculatePercentage($total, $part)
    {
        if ($total == 0) {
            return 0;
        }
        return ($part / $total) * 100;
    }

    /**
     * Get status color
     */
    public static function getStatusColor($status)
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'paid' => 'success',
            'partial' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'delivered' => 'info',
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Get payment status badge
     */
    public static function getPaymentStatusBadge($status)
    {
        $badges = [
            'paid' => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-warning">Partial</span>',
            'pending' => '<span class="badge bg-danger">Pending</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get stock status badge
     */
    public static function getStockStatusBadge($current, $minimum)
    {
        if ($current <= 0) {
            return '<span class="badge bg-danger">Out of Stock</span>';
        } elseif ($current <= $minimum) {
            return '<span class="badge bg-warning">Low Stock (' . $current . ')</span>';
        } else {
            return '<span class="badge bg-success">' . $current . ' in Stock</span>';
        }
    }

    /**
     * Truncate text
     */
    public static function truncate($text, $length = 50, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Get current date time
     */
    public static function getCurrentDateTime()
    {
        return now()->format('Y-m-d H:i:s');
    }

    /**
     * Get current date
     */
    public static function getCurrentDate()
    {
        return now()->format('Y-m-d');
    }

    /**
     * Format date
     */
    public static function formatDate($date, $format = 'd M, Y')
    {
        if (!$date) {
            return 'N/A';
        }
        return \Carbon\Carbon::parse($date)->format($format);
    }

    /**
     * Format date time
     */
    public static function formatDateTime($date, $format = 'd M, Y H:i')
    {
        if (!$date) {
            return 'N/A';
        }
        return \Carbon\Carbon::parse($date)->format($format);
    }

    /**
     * Get time ago
     */
    public static function timeAgo($date)
    {
        if (!$date) {
            return 'N/A';
        }
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }

    /**
     * Validate phone number
     */
    public static function validatePhone($phone)
    {
        return preg_match('/^[0-9+\-\s()]+$/', $phone);
    }

    /**
     * Validate email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Get user IP
     */
    public static function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    /**
     * Get browser info
     */
    public static function getBrowserInfo()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    /**
     * Generate random color
     */
    public static function randomColor()
    {
        $colors = [
            '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545',
            '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0',
            '#0d6efd', '#6c757d', '#343a40', '#f8f9fa', '#212529'
        ];
        return $colors[array_rand($colors)];
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Get server info
     */
    public static function getServerInfo()
    {
        return [
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
        ];
    }

    /**
     * Check if string contains another string
     */
    public static function stringContains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Array to object
     */
    public static function arrayToObject($array)
    {
        return json_decode(json_encode($array));
    }

    /**
     * Object to array
     */
    public static function objectToArray($object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * Get week days
     */
    public static function getWeekDays()
    {
        return [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 
            'Friday', 'Saturday', 'Sunday'
        ];
    }

    /**
     * Get months
     */
    public static function getMonths()
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
    }

    /**
     * Get years
     */
    public static function getYears($start = 2020, $end = null)
    {
        $end = $end ?? date('Y');
        $years = [];
        for ($i = $start; $i <= $end; $i++) {
            $years[] = $i;
        }
        return $years;
    }

    /**
     * Sanitize input
     */
    public static function sanitize($input)
    {
        return strip_tags(trim($input));
    }

    /**
     * Escape HTML
     */
    public static function escapeHtml($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}