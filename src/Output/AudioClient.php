<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Alice\Output;

use Alice\Socket\SocketClient;
use Alice\Socket\SocketMessage;

use PhpGpio\Gpio;

/**
 * ALICE Output Client
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package alice-output
 */
class AudioClient extends SocketClient {

    const FORMAT = 'wav';
    const EXTENSION = 'wav';

    const ALERT_INFO = 'alert_info';
    const ALERT_NOTIFY = 'alert_notify';
    const ALERT_TONE = 'alert_tone';

    const ALERT_START_LISTEN = 'alert_start_listen';
    const ALERT_STOP_LISTEN = 'alert_stop_listen';

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
        $this->settings = Output::go()->config()->get('sensor');
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
        $this->log("received alert: {$alertType}");

        $alertFile = "alert_{$alertType}.".self::EXTENSION;
        $audioPath = paths($this->assetPath, 'sounds', $alertFile);

        if (!file_exists($audioPath)) {
            $this->log(" unknown alert type");
            return;
        }

        exec("pacmd play-file \"{$audioPath}\"", null, $return);
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