<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Alice\Output;

use Alice\Output;

use Alice\Socket\SocketClient;
use Alice\Socket\SocketMessage;

/**
 * ALICE Output Client
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package alice-output
 */
class AudioClient extends SocketClient {

    const FORMAT = 'wav';
    const EXTENSION = 'wav';

    /**
     * Sound asset path
     * @var string
     */
    protected $assetPath;

    /**
     * Construct
     *
     */
    public function __construct() {
        parent::__construct();
        $this->settings = Output::go()->config()->get('output');
        $this->server = Output::go()->config()->get('server');
        $this->assetPath = paths(\Alice\Daemon\Daemon::option('appDir'), 'assets');
    }

    /**
     * Register output client
     *
     */
    public function registerClient() {
        $this->rec("registering client");
        $output = Output::go()->config()->get('output');
        $this->sendMessage('register', $output);
    }

    /**
     * Tick
     *
     */
    public function tick() {
        // Stay connected
        $connected = parent::tick();
        if (!$connected) {
            return;
        }

        // Don't do anything if we're not connected
        if (!$this->isReady()) {
            return;
        }

        // Do our stuff here

    }

    /**
     * Receive alert
     *
     * @param SocketMessage $message
     */
    public function message_alert(SocketMessage $message) {
        $alert = $message->getData();
        $alertType = val('type', $alert);
        $this->rec("received alert: {$alertType}");

        $alertFile = "alert_{$alertType}.".self::EXTENSION;
        $audioPath = paths($this->assetPath, 'sounds', $alertFile);

        if (!file_exists($audioPath)) {
            $this->rec(" unknown alert type");
            return;
        }

        $out = [];
        //exec("pacmd play-file \"{$audioPath}\"", $out, $return);
        exec("afplay -q 1 \"{$audioPath}\"", $out, $return);
    }

    /**
     * Receive TTS
     *
     * @param SocketMessage $message
     */
    public function message_tts(SocketMessage $message) {

    }

    /**
     * Receive stream
     *
     * @param SocketMessage $message
     */
    public function message_stream(SocketMessage $message) {

    }

    /**
     * Receive stream control
     *
     * @param SocketMessage $message
     */
    public function message_streamctl(SocketMessage $message) {

    }

}