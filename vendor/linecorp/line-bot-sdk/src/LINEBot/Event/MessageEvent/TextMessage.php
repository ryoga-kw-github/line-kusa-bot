<?php

namespace LINE\LINEBot\Event\MessageEvent;

use LINE\LINEBot\Event\MessageEvent;

/**
 * A class that represents the message event of text.
 *
 * @package LINE\LINEBot\Event\MessageEvent
 */
class TextMessage extends MessageEvent
{
    /**
     * Emoji Info List
     *
     * @var array|null
     */
    private $emojis;

    /**
     * TextMessage constructor.
     *
     * @param array $event
     */
    public function __construct($event)
    {
        parent::__construct($event);
        if (isset($this->message['emojis'])) {
            $this->emojis = array_map(function ($emojiInfo) {
                return new EmojiInfo($emojiInfo);
            }, $this->message['emojis']);
        }
    }

    /**
     * Returns text of the message.
     *
     * @return string
     */
    public function getText()
    {
        return $this->message['text'];
    }

    /**
     * Returns emoji info list of the messages.
     *
     * @return array
     */
    public function getEmojis()
    {
        return $this->emojis;
    }
}
