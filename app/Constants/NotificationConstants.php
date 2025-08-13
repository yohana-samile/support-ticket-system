<?php

namespace App\Constants;

class NotificationConstants
{
    // Notification channels
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_BOTH = 'both';
    const CHANNEL_NONE = 'none';

    public static function getAllChannels(): array
    {
        return [
            self::CHANNEL_EMAIL,
            self::CHANNEL_SMS,
            self::CHANNEL_BOTH,
            self::CHANNEL_NONE,
        ];
    }

    // Notification types
    const TYPE_NEW = 'new';
    const TYPE_REMINDER = 'reminder';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_NEW,
            self::TYPE_REMINDER,
        ];
    }

    // Theme options
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';
}
