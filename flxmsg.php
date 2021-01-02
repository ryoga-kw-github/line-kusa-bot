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

            case $text === '体調管理':
                $taityo_good = new PostbackTemplateActionBuilder($label='良い', $data='taityo=good', $displayText='体調が良い');
                $taityo_bad = new PostbackTemplateActionBuilder($label='悪い', $data='taityo=bad', $displayText='体調がよくない');
                $taityo_actions = [$taityo_good, $taityo_bad];
                $taityo_confirm = new ConfirmTemplateBuilder('今日の体調は？', $taityo_actions);
                //Confirmメッセージの本体
                $taityo_confirm_message = new TemplateMessageBuilder('confirm', $taityo_confirm);
                //replyMessageという関数で先程作ったConfirmメッセージをトークに送信
                $bot->replyMessage($reply_token, $taityo_confirm_message);

                //ボタンを押したときのデータを取得
                $postback_data = $event->getPostbackData();
                parse_str($postback_data, $data);

                break;

            //「体調管理」で「良い」を選択した場合
            case $data === 'taityo=good':
                $bot->replyText($reply_token, 'よかったです！このまま感染症対策を徹底しましょう！');
                break;

            //「体調管理」で「悪い」を選択した場合
            case $data === 'taityo=bad':
                $hatunetu_yes = new PostbackTemplateActionBuilder('ある', 'hatunetu=yes', '熱がある');
                $hatunetu_no = new PostbackTemplateActionBuilder('ない', 'hatunetu=no','熱はない');
                $hatunetu_actions = [$hatunetu_yes, $hatunetu_no];
                $hatunetu_confirm = new ConfirmTemplateBuilder('熱はありますか？',$hatunetu_actions);
                $hatunetu_confirm_message = new TemplateMessageBuilder('confirm', $hatunetu_confirm);

                $bot->replyMessage($reply_token, $hatunetu_confirm_message);

                //ボタンを押したときのデータを取得
                $postback_data = $event->getPostbackData();
                parse_str($postback_data, $data);
            
             //「熱がある」場合
            case $data === 'hatunetu=yes':
                $bot->replyText($reply_token, '37.5度以上の発熱が4日間以上(高齢者の場合は2日間以上)続くのであれば、帰国者・接触者相談センターへ相談することをお勧めします。');
                break;

            //「熱はない」場合
            case $data === 'hatunetu=no':
                $bot->replyText($reply_token, '体温が37.5度以下でも、強い倦怠感や息苦しさがある場合は、帰国者・接触者相談センターへ相談することをお勧めします。');
                break;

            default:
            $bot->replyText($reply_token, 'ﾜﾚﾜﾚﾊ ｳﾁｭｳｼﾞﾝﾀﾞ'); 
        }
    }
}


//ボタンを押したときのデータを取得
//$postback_data = $event->getPostbackData();
//parse_str($postback_data, $data);