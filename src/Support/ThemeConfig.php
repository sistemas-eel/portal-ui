<?php

namespace SistemasEel\PortalUi\Support;

class ThemeConfig
{
    /**
     * Retorna toda a configuração de marca ou uma chave específica.
     *
     * @return mixed
     */
    public static function brand($key = null, $default = null)
    {
        return $key
            ? config("portal-ui.brand.{$key}", $default)
            : config('portal-ui.brand', []);
    }

    /**
     * @return mixed
     */
    public static function color($key, $default = null)
    {
        return config("portal-ui.colors.{$key}", $default);
    }

    /**
     * @return mixed
     */
    public static function asset($key, $default = null)
    {
        return config("portal-ui.assets.{$key}", $default);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function navigationGroups()
    {
        return config('portal-ui.navigation.groups', []);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function visibleNavigationGroups()
    {
        return Navigation::groups();
    }

    /**
     * @return mixed
     */
    public static function route($name, $default = null)
    {
        return config("portal-ui.routes.{$name}", $default);
    }

    /**
     * @return array<int, string>
     */
    public static function flashKeys($level)
    {
        return config("portal-ui.flash.{$level}_keys", []);
    }
}
