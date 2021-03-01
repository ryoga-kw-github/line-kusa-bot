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

            case strpos($text, '宇宙人') !==false:
                $bot->replyText($reply_token, 'ﾜﾚﾜﾚﾊ ｳﾁｭｳｼﾞﾝﾀﾞ');
                break;

            case $text === 'Bot説明':
                $bot->replyText($reply_token, "単語を送ると返事してくれます。\n
                    1.草\n
                    2.あーはん？\n
                    3.宇宙人\n
                    4.兵庫\n
                    5.ヒョギフ\n
                    6.群馬\n
                    7.グンタマ\n
                    8.山形\n
                    9.ヤマイドウ\n
                    10.愛媛\n
                    11.エヒフ\n
                    12.茨城\n
                    13.応援\n
                    14.千葉\n
                    15.佐賀"
                );
                break;

            case $text === '兵庫':
                $bot->replyText($reply_token, '岐阜');
                break;

            case $text === 'ヒョギフ':
                $bot->replyText($reply_token, 'ヒョギフ大統領の貴重な産卵シーン');
                break;

            case $text === '群馬':
                $bot->replyText($reply_token, '埼玉');
                break;

            case $text === 'グンタマ':
                $bot->replyText($reply_token, 'オオグンタマの貴重な産卵シーン');

            case $text === '山形':
                $bot->replyText($reply_token, '北海道');
                break;

            case $text === 'ヤマイドウ':
                $bot->replyText($reply_token, '全日本アマチュアヤマイドウ選手権大会');
                break;

            case $text === '愛媛':
                $bot->replyText($reply_token, '岐阜');
                break;

            case $text === 'エヒフ':
                $bot->replyText($reply_token, 'オオグンタマのエヒフ');
                break;

            case $text === '茨城':
                $bot->replyText($reply_token, 'イｳﾞｧーﾙﾙﾙｱキｲー');
                break;

            case strpos($text, '応援') !==false:
                $bot->replyText($reply_token, 'ｶﾞﾝﾊﾞﾚｰ!!');
                break;

            case $text === '千葉':
                $bot->replyText($reply_token, '茨城');
                break;

            case $text === 'チバラキ':
                $bot->replyText($reply_token, 'チバーﾙﾙﾙｱキｲー');
                break;

            case $text === '奈良':
                $bot->replyText($reply_token, 'ナーﾙﾙﾙｱー');
                break;
            
            case $text === '千葉':
                $bot->replyText($reply_token, '滋賀');
                break;

            case $text === '佐賀':
                $bot->replyText($reply_token, '千葉滋賀佐賀');
                break;
        }
    }
}