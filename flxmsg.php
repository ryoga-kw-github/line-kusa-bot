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

/*

foreach ($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $reply_token = $event->getReplyToken();
        $text = $event->getText();
        //$bot->replyText($reply_token, $text);

        switch ($text) {
            case $text === 'どっち':
                $bot->replyText($reply_token, 'Flxmsg.php');
                //LINE DevelopersでWebhookにどっちを設定したか判断する
                break;

            case $text === '説明':
                $bot->replyText($reply_token, 'FlexMessageでボタンを表示します');
                break;
        }
    }
}

*/

  
  //Confirmテンプレート返信。引数はLINEBot、返信先、代替テキスト、本文、アクション(可変長引数)
  function replyConfirmTemplate($bot, $replyToken, $alternativeText,$text, ...$actions) {
    
    $actionArray = array();

    foreach($actions as $value) {
      array_push($actionArray, $value);
    }

    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
      $alternativeText,

      // Confirmテンプレートの引数はテキスト、アクションの配列
      new \LINE\LINEBot\MessageBuilder\ConfirmTemplateBuilder($text,$actionArray)
    );

    $response = $bot->replyMessage($replyToken, $builder);

    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }

  }



  function __construct() {

    $json_string = file_get_contents('php://input');
    $jsonObj = json_decode($json_string);
    $this->userId = $jsonObj->{"events"}[0]->{"source"}->{"userId"};
    $this->replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

    $this->httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($this->token);
    $this->bot = new \LINE\LINEBot($this->httpClient, ['channelSecret' => $this->secret]);

    $this->replyConfirmTemplate(
            $this->bot,
            $this->replyToken,
            "test",
            "test",
            [
                new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("Yes", "Yes"),
                new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("No", "No"),
            ]
        );
}