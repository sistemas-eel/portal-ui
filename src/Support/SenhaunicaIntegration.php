<?php

namespace SistemasEel\PortalUi\Support;

final class SenhaunicaIntegration
{
    public const DEFAULT_PROVIDER = 'Uspdev\\SenhaunicaSocialite\\SenhaunicaServiceProvider';

    public static function providerClass(): string
    {
        return (string) config(
            'portal-ui.integrations.senhaunica.provider',
            self::DEFAULT_PROVIDER
        );
    }

    public static function isInstalled(): bool
    {
        return class_exists(self::providerClass());
    }

    public static function isRequested(): bool
    {
        return (bool) config(
            'portal-ui.integrations.senhaunica.enabled',
            self::isInstalled()
        );
    }

    public static function isEnabled(): bool
    {
        return self::isRequested() && self::isInstalled();
    }
}
