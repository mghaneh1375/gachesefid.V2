<?php
// Load composer
require __DIR__ . '/../../../vendor/autoload.php';

$bot_api_key  = '549593153:AAHzu5teXN-GafeeE8df90csseA47OXjD6w';
$bot_username = '@gachesefidBot';
$hook_url = 'https://dev.shazdemosafer.com/hook.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    // echo $e->getMessage();
}