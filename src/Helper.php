<?php
/**
 * Created by PhpStorm.
 * User: zlovelady
 * Date: 11/22/17
 * Time: 4:35 PM
 */

namespace BlueInk\ApiClient;


class Helper
{
    const STATUS_NEW = 'ne';
    const STATUS_DRAFT = 'dr';
    const STATUS_READY = 're';
    const STATUS_SENT = 'se';
    const STATUS_STARTED = 'st';
    const STATUS_CANCELLED = 'ca';
    const STATUS_EXPIRED = 'ex';
    const STATUS_COMPLETE = 'co';
    const STATUS_FAILED = 'fa';

    const STATUSES_TERMINAL = [
        self::STATUS_CANCELLED, self::STATUS_EXPIRED,
        self::STATUS_COMPLETE, self.STATUS_FAILED
    ];

    static function isTerminalStatus($status) {
        return in_array($status, self::TERMINAL_STATUSES);
    }
}