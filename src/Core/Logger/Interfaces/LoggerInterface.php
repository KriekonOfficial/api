<?php
namespace Core\Logger\Interfaces;

use Core\Logger\Model\LogModel;

interface LoggerInterface
{
    public function __construct(LogModel $model);

	/**
     * System is unusable.
     *
     * @return void
     */
    public function emergency() : void;

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @return void
     */
    public function alert() : void;

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @return void
     */
    public function critical() : void;

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @return void
     */
    public function error() : void;

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @return void
     */
    public function warning() : void;

    /**
     * Normal but significant events.
     *
     * @return void
     */
    public function notice() : void;

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @return void
     */
    public function info() : void;

    /**
     * Detailed debug information.
     *
     * @return void
     */
    public function debug() : void;

    /**
     * Logs with an arbitrary level.
     *
     * @return void
     */
    public function log() : void;
}
