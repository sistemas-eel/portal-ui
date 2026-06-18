<?php

namespace SistemasEel\PortalUi\Tests\Feature;

use SistemasEel\PortalUi\Support\ThemeConfig;
use SistemasEel\PortalUi\Tests\TestCase;

class ThemeConfigTest extends TestCase
{
    public function test_theme_config_retrieves_brand_settings(): void
    {
        $this->assertEquals('Portal Exemplo', ThemeConfig::brand('name'));
        $this->assertEquals('Atendimento digital', ThemeConfig::brand('subtitle'));
        $this->assertArrayHasKey('name', ThemeConfig::brand());
    }

    public function test_theme_config_retrieves_colors(): void
    {
        config(['portal-ui.colors.primary' => '#00ff00']);
        $this->assertEquals('#00ff00', ThemeConfig::color('primary'));
    }

    public function test_theme_config_retrieves_assets(): void
    {
        config([
            'portal-ui.assets.fontawesome_cdn' => true,
            'portal-ui.assets.fontawesome_cdn_url' => 'https://cdn.example.test/fontawesome.css',
        ]);
        $this->assertTrue(ThemeConfig::asset('fontawesome_cdn'));
        $this->assertEquals('https://cdn.example.test/fontawesome.css', ThemeConfig::asset('fontawesome_cdn_url'));
    }

    public function test_theme_config_retrieves_navigation_groups(): void
    {
        config(['portal-ui.navigation.groups' => [['label' => 'Grupo Teste']]]);
        $this->assertEquals([['label' => 'Grupo Teste']], ThemeConfig::navigationGroups());
    }

    public function test_theme_config_retrieves_routes(): void
    {
        config(['portal-ui.routes.login' => 'custom-login']);
        $this->assertEquals('custom-login', ThemeConfig::route('login'));
    }

    public function test_theme_config_retrieves_flash_keys(): void
    {
        config(['portal-ui.flash.success_keys' => ['success', 'message']]);
        $this->assertEquals(['success', 'message'], ThemeConfig::flashKeys('success'));
    }
}
