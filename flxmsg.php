<?php

//LINE SDKの読み込み
require_once __DIR__ . '/vendor/autoload.php';
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$sign = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

$events = $bot->parseEventRequest(file_get_contents('php://input'), $sign);


foreach ($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $reply_token = $event->getReplyToken();
        $text = $event->getText();
        //$bot->replyText($reply_token, $text);

        switch ($text) {
            case strpos($text, '草') !==false:
                $bot->replyText($reply_token, '草言うな');
                break;
            
            case $text === 'あーはん？':
                $bot->replyText($reply_token, '黙れよ');
                break;

            case $text === '説明':
                $bot->replyText($reply_token, '特定の単語を送ると返事してくれます');
                break;
        }
    }
}