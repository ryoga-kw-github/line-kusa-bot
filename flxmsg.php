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

foreach ((array)$events as $event){
    // MessageEventクラスのインスタンスでなければ処理をスキップ
    if(!($event instanceof \LINE\LINEBot\Event\MessageEvent)){
      error_log('Non Message event has come');
      continue;
    }
    // TextMessageBuilderクラスのインスタンスでなければ処理をスキップ
    if(!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)){
      error_log('Non Message event has come');
      continue;
    }
    //オウム返し
    $bot->replyText($event->getReplyToken(), $event->getText());
  
  }
  
  //テキストを返信。引数はLINEBot、返信先、テキスト
  function replyTextMessage($bot,$replyToken,$text) {
    // 返信を行いメッセージを取得
    // TextMessageBuilderの引数はテキスト
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
  
    //レスポンスが異常な場合
    if(!$response->isSucceeded()){
      //エラー内容を出力
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  //画像を返信。引数はLINEBot、返信先、画像URL、サムネイルURL
  function replyImageMessage($bot,$replyToken,$originalImageUrl,$previewImageUrl){
    // ImageMessageBuilderの引数は画像URL、サムネイルURL
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  //位置情報を返信。引数はLINEBot、返信先、タイトル、住所、緯度、経度
  function replyLocationMessage($bot, $replyToken, $title, $address, $lat, $lon) {
    //LocationMessageBuilderの引数はダイアログのタイトル、住所、緯度、経度
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title,$address,$lat,$lon));
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  //スタンプを返信。引数はLINEBot、返信先、スタンプのパッケージID、スタンプID
  function replyStickerMessage($bot, $replyToken, $packageId, $stickerId) {
    //StickerMessageBuilderの引数はスタンプのパッケージID、スタンプID
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId, $stickerId));
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  //動画を返信。引数はLINEBot、返信先、動画URL、サムネイルURL
  function replyVideoMessage($bot, $replyToken, $originalContentUrl, $previewImageUrl) {
    //VideoMessageBuilderの引数は動画URL、サムネイルURL
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder($originalContentUrl, $previewImageUrl));
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  //オーディオファイルを返信。引数はLINEBot、返信先、ファイルのURL、ファイルの再生時間
  function replyAudioMessage($bot, $replyToken, $originalContentUrl, $audioLength) {
    //AudioMessageBuilderの引数は動画URL、サムネイルURL
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\AudioMessageBuilder($originalContentUrl, $audioLength));
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  //複数のメッセージをまとめて返信。引数はLINEBot、返信先、メッセージ(可変長引数)
  function replyMultiMessage($bot, $replyToken, ...$msgs) {
    //MultiMessageBuilderをインスタンス化
    $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    // ビルダーにメッセージをすべて追加
    foreach($msgs as $value){
      $builder->add($value);
    }
    $response = $bot->replyMessage($replyToken,$builder);
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
  // Buttonsテンプレートを送信。引数はLINEBot、返信先、代替テキスト、画像URL、タイトル、本文、アクション(可変長引数)
  function replyButtonsTemplate($bot, $replyToken, $alternativeText,$imageUrl,$title,$text, ...$actions) {
    // アクションを格納する配列
    $actionArray = array();
    // アクションをすべて追加
    foreach($actions as $value) {
      array_push($actionArray, $value);
    }
    //TemplateMessageBuilderの引数は代替テキスト、ButtonTemplateBuilder
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
      $alternativeText,
      // ButtonTemplateBuilderの引数はタイトル、本文
      // 画像URL、アクションの配列
      new \LINE\LINEBot\MessageBuilder\ButtonTemplateBuilder($title,$text,$imageUrl,$actionArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }
  
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
  
  //Carouselテンプレートを返信。引数はLINEBot、返信先、メッセージ(可変長引数)
  //ダイアログの配列
  function replyCarouselTemplate($bot, $replyToken, $alternativeText, $columnArray) {
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
      $alternativeText,
      // Carouselテンプレートの引数はダイアログの配列
      new \LINE\LINEBot\MessageBuilder\CarouselTemplateBuilder($columnArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' '.$response->getRawBody());
    }
  }