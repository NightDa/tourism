<?php
function logMessage($message, $level = 'info')
{
    $log_dir = __DIR__ . '/../logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $log_file = $log_dir . '/app-' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;

    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

function logError($message, $context = [])
{
    $context_str = !empty($context) ? ' | ' . json_encode($context) : '';
    logMessage($message . $context_str, 'ERROR');
}

function logWarning($message, $context = [])
{
    $context_str = !empty($context) ? ' | ' . json_encode($context) : '';
    logMessage($message . $context_str, 'WARNING');
}
