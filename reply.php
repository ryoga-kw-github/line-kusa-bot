<?php

use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;

$httpClient = new CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$signature = $_SERVER['HTTP_' . HTTPHeader::LINE_SIGNATURE];
$http_request_body = file_get_contents('php://input');
$events = $bot->parseEventRequest($http_request_body, $signature);
$event = $events[0];
$reply_token = $event->getReplyToken();


$categories = [
    '和食',
    '洋食',
    '中華料理',
    'アジア・エスニック',
    'イタリアン',
    'フレンチ'
];

foreach ($categories as $category) {
    // 1、表示する文言と押下時に送信するメッセージをセット
    $message_template_action_builder = new MessageTemplateActionBuilder($category, $category . 'を選択したよ！');
    // 2、1をボタンに組み込む
    $quick_reply_button_builder = new QuickReplyButtonBuilder($message_template_action_builder);
    // 3、ボタンを配列に格納する(12個まで)
    $quick_reply_buttons[] = $quick_reply_button_builder;
}
// 4、3を元にクイックリプライを作成する
$quick_reply_message_builder = new QuickReplyMessageBuilder($quick_reply_buttons);
$text_message_builder = new TextMessageBuilder('カテゴリを選択してください', $quick_reply_message_builder);
$bot->replyMessage($reply_token, $text_message_builder);