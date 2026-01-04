<?php
declare (strict_types = 1);

class LanguageService
{
    public static function getCurrentLanguage(): string
    {
        if (! empty($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        if (! empty($_SESSION['user_id'])) {
            return FaithGuardRepository::getUserLanguage((int) $_SESSION['user_id']);
        }
        return 'en';
    }

    public static function getBibleTranslation(): string
    {
        return self::getCurrentLanguage() === 'nl' ? 'NBV21' : 'NRSVUE';
    }
}
