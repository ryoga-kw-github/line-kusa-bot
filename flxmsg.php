<?php

//LINE SDKの読み込み
require_once __DIR__ . '/vendor/autoload.php';
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

$httpClient = new CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$sign = $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE];

$events = $bot->parseEventRequest(file_get_contents('php://input'), $sign);
$event = $events[0];

foreach ($events as $event) {
    if ($event instanceof TextMessage) {

        //入力したテキストを取得して$textに入れる
        $reply_token = $event->getReplyToken();
        $text = $event->getText();

        //$textにどんなテキストが入ってるかによって分岐するSwitch文
        switch ($text) {
            case $text === 'どっち':
                $bot->replyText($reply_token, 'Flxmsg.php');
                //LINE DevelopersでWebhookにどっちを設定したか判断する
                break;

            case $text === 'confirm':
                $yes_confirm = new PostbackTemplateActionBuilder('はい', 'confirm=1');
                $no_confirm = new PostbackTemplateActionBuilder('いいえ', 'confirm=0');

                $actions = [$yes_confirm, $no_confirm];

                $confirm = new ConfirmTemplateBuilder('メッセージ', $actions);
                $confirm_message = new TemplateMessageBuilder('confirm', $confirm);

                $bot->replyMessage($reply_token, $confirm_message);

                //ボタンを押したときのデータを取得
                $postback_data = $event->getPostbackData();
                parse_str($postback_data, $data);

                break;

            case $text === 'でーたてすと':
                $bot->replyMessage($reply_token, $data);
                break;

            //------------------------------------------------------------------------


            //上のConfirmメッセージでボタンを押したときにメッセージを送らせたい
                        
            case $data === 'confirm=1':
                $bot->replyMessage($reply_token, 'aiueo');
                break;

            case $data === 'confirm=0':                
                $bot->replyMessage($reply_token, '????????');
                break;
            
            //-------------------------------------------------------------------------

        }
    }
}