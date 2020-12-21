<?php

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


$access_token = new CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

$json_string = file_get_contents('php://input');

$json_obj = json_decode($json_string);

$reply_token = $json_obj->{'events'}[0]->{'replyToken'};

$type = $json_obj->{'events'}[0]->{'type'};

$msg_obj = $json_obj->{'events'}[0]->{'message'}->{'type'};

if($type === 'message') {
    if($msg_obj === 'text') {
        $msg_text = $json_obj->{'events'}[0]->{'message'}->{'text'};
        if($msg_text === '予約') {
            $message = array(
                'type' => 'template',
                'altText' => 'いつのご予約ですか？',
                'template' => array(
                    'type' => 'confirm',
                    'text' => 'いつのご予約ですか？',
                    'actions' => array(
                        array(
                            'type' => 'postback',
                            'label' => '予約しない',
                            'data' => 'action=back'
                        ), array(
                            'type' => 'datetimepicker',
                            'label' => '期日を指定',
                            'data' => 'datetemp',
                            'mode' => 'date'
                        )
                    )
                )
            );
        } else {
            $message = array(
                'type' => 'text',
                'text' => '【'.$msg_text.'】とは何ですか？'
            );
        }
    } elseif($msg_obj === 'location') {
        // 位置情報を受け取った時
        $message = array(
            'type' => 'location',
            'title' => '皇居',
            'address' => '〒100-8111 東京都千代田区千代田１−１',
            'latitude' => 35.683798,
            'longitude' => 139.754182
        );
    }
} else if($type === 'postback') {
    // ポストバック受け取り時

    // 送られたデータ
    $postback = $json_obj->{'events'}[0]->{'postback'}->{'data'};

    if($postback === 'datetemp') {
        // 日にち選択時
        $message = array(
            'type' => 'text',
            'text' => '【'.$json_obj->{'events'}[0]->{'postback'}->{'params'}->{'date'}.'】にご予約を承りました。'
        );
    } elseif($postback === 'action=back') {
        // 戻る選択時
        $message = array(
            'type' => 'text',
            'text' => '何もしませんでした。'
        );
    }
}

$post_data = array(
    'replyToken' => $reply_token,
    'messages' => array($message)
);

// CURLでメッセージを返信する
$ch = curl_init('https://api.line.me/v2/bot/message/reply');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer ' . $access_token
));
$result = curl_exec($ch);
curl_close($ch);

?>