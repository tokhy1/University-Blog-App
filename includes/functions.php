<?php
require_once ROOT_PATH . '/classes/Database.php';

function csrf_field()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die('Invalid request.');
    }
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function old($key, $default = '')
{
    return $_POST[$key] ?? $default;
}

function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function time_ago($datetime)
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

function paginate($current, $totalItems, $perPage, $baseUrl)
{
    $totalPages = ceil($totalItems / $perPage);
    if ($totalPages <= 1) return '';

    $html = '<div class="pagination">';
    if ($current > 1) {
        $html .= '<a href="' . $baseUrl . ($current - 1) . '" class="page-link">&laquo; Prev</a>';
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $current) {
            $html .= '<span class="page-link active">' . $i . '</span>';
        } elseif ($i == 1 || $i == $totalPages || abs($i - $current) <= 2) {
            $html .= '<a href="' . $baseUrl . $i . '" class="page-link">' . $i . '</a>';
        } elseif (abs($i - $current) == 3) {
            $html .= '<span class="page-dots">...</span>';
        }
    }

    if ($current < $totalPages) {
        $html .= '<a href="' . $baseUrl . ($current + 1) . '" class="page-link">Next &raquo;</a>';
    }
    $html .= '</div>';
    return $html;
}

function set_flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash()
{
    if (!isset($_SESSION['flash'])) return null;
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function get_initials($name)
{
    $words = explode(' ', trim($name));
    if (count($words) >= 2) {
        return strtoupper($words[0][0] . $words[1][0]);
    }
    return strtoupper(substr($words[0], 0, 2));
}

function parse_markdown($text)
{
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // Code blocks
    $text = preg_replace('/```(\w*)\n(.*?)```/s', '<pre><code>$2</code></pre>', $text);

    // Inline code
    $text = preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $text);

    // Headers
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);

    // Bold + italic
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

    // Images
    $text = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1" class="content-image">', $text);

    // Links
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);

    // Blockquotes
    $text = preg_replace('/^&gt; (.+)$/m', '<blockquote>$1</blockquote>', $text);

    // Horizontal rules
    $text = preg_replace('/^---$/m', '<hr>', $text);

    // Unordered lists
    $text = preg_replace('/^[-*] (.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/((?:<li>.*<\/li>\n?)+)/', '<ul>$1</ul>', $text);

    // Ordered lists
    $text = preg_replace('/^\d+\. (.+)$/m', '<li>$1</li>', $text);

    // Paragraphs - wrap lines that aren't already inside block elements
    $lines = explode("\n", $text);
    $result = [];
    $inParagraph = false;

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (empty($trimmed)) {
            if ($inParagraph) {
                $result[] = '</p>';
                $inParagraph = false;
            }
            continue;
        }

        if (preg_match('/^<(h[1-6]|ul|ol|li|pre|blockquote|hr|div|table)/', $trimmed)) {
            if ($inParagraph) {
                $result[] = '</p>';
                $inParagraph = false;
            }
            $result[] = $trimmed;
        } else {
            if (!$inParagraph) {
                $result[] = '<p>';
                $inParagraph = true;
            }
            $result[] = $trimmed;
        }
    }
    if ($inParagraph) $result[] = '</p>';

    return implode("\n", $result);
}

function excerpt($text, $length = 150)
{
    $plain = strip_tags($text);
    if (strlen($plain) <= $length) return $plain;
    return substr($plain, 0, $length) . '...';
}
