<?php

namespace SistemasEel\PortalUi\Support;

use Illuminate\Support\Facades\Route;

/**
 * Helpers para normalizar a configuração de navegação antes da renderização.
 *
 * @phpstan-type NavigationItem array{
 *     label?: string,
 *     route?: string|null,
 *     url?: string|null,
 *     href?: string|null,
 *     icon?: string|null,
 *     active?: string|null,
 *     can?: string|null,
 *     guest?: bool|null,
 *     external?: bool|string|null,
 *     target?: string|null,
 *     rel?: string|null,
 *     is_active?: bool|null,
 *     is_external?: bool|null,
 *     children?: list<array<string, mixed>>
 * }
 * @phpstan-type NormalizedNavigationItem NavigationItem&array{
 *     children: list<array<string, mixed>>,
 *     has_children: bool,
 *     href: string,
 *     is_active: bool,
 *     is_external: bool,
 *     target: string|null,
 *     rel: string|null
 * }
 * @phpstan-type NavigationGroup array{
 *     label?: string,
 *     items?: list<array<string, mixed>>
 * }
 */
class Navigation
{
    /**
     * Filtra grupos sem itens visíveis e normaliza os itens restantes.
     *
     * @param array<string, NavigationGroup>|null $groups
     * @return array<string, array<string, mixed>>
     */
    public static function groups(?array $groups = null): array
    {
        $groups = $groups ?? config('portal-ui.navigation.groups', []);
        $visibleGroups = [];

        foreach ($groups as $groupKey => $group) {
            $items = self::items($group['items'] ?? []);

            if (count($items) > 0) {
                $visibleGroups[$groupKey] = array_merge($group, ['items' => $items]);
            }
        }

        return $visibleGroups;
    }

    /**
     * Normaliza uma lista de itens descartando os que não devem ser exibidos.
     *
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    public static function items(array $items): array
    {
        $visibleItems = [];

        foreach ($items as $item) {
            $normalizedItem = self::item($item);

            if ($normalizedItem !== null) {
                $visibleItems[] = $normalizedItem;
            }
        }

        return $visibleItems;
    }

    /**
     * Resolve visibilidade, filhos, estado ativo e metadados de link do item.
     *
     * @param array<string, mixed> $item
     * @return array<string, mixed>|null
     */
    public static function item(array $item): ?array
    {
        $children = self::items($item['children'] ?? []);
        $hasChildren = count($children) > 0;
        $hasOwnHref = self::hasHref($item);
        $isVisible = self::passesVisibilityRules($item) && ($hasOwnHref || $hasChildren);

        if (! $isVisible) {
            return null;
        }

        return array_merge($item, [
            'children' => $children,
            'has_children' => $hasChildren,
            'href' => self::href($item),
            'is_active' => self::isActive($item, $children),
            'is_external' => self::isExternal($item),
            'target' => self::target($item),
            'rel' => self::rel($item),
        ]);
    }

    /**
     * Determina o href final do item considerando href explícito, url e route.
     *
     * @param array<string, mixed> $item
     */
    public static function href(array $item): string
    {
        $routeName = $item['route'] ?? null;

        if (! empty($item['href'])) {
            return $item['href'];
        }

        if (! empty($item['url'])) {
            return $item['url'];
        }

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        return '#';
    }

    /**
     * Considera o item ativo quando sua própria regra bate ou algum filho está ativo.
     *
     * @param array<string, mixed> $item
     * @param list<array<string, mixed>> $children
     */
    public static function isActive(array $item, array $children = []): bool
    {
        if (! empty($item['is_active'])) {
            return true;
        }

        $activePattern = $item['active'] ?? ($item['route'] ?? null);

        if ($activePattern && request()->routeIs($activePattern)) {
            return true;
        }

        foreach ($children as $child) {
            if (! empty($child['is_active'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta links externos automaticamente para URLs absolutas.
     *
     * @param array<string, mixed> $item
     */
    public static function isExternal(array $item): bool
    {
        if (isset($item['external'])) {
            return filter_var($item['external'], FILTER_VALIDATE_BOOLEAN);
        }

        $url = $item['url'] ?? '';

        return is_string($url) && preg_match('/^https?:\/\//i', $url) === 1;
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function passesVisibilityRules(array $item): bool
    {
        $user = auth()->user();
        $can = $item['can'] ?? null;
        $guest = $item['guest'] ?? null;
        $hasCan = $user && method_exists($user, 'can');
        $isAllowed = ! $can || ($hasCan && $user->can($can));
        $isGuestAllowed = ! $guest || is_null($user);

        return $isAllowed && $isGuestAllowed;
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function hasHref(array $item): bool
    {
        $routeName = $item['route'] ?? null;
        $url = $item['url'] ?? null;
        $href = $item['href'] ?? null;
        $routeExists = $routeName ? Route::has($routeName) : false;
        $hideMissingRoutes = (bool) config('portal-ui.navigation.hide_missing_routes', true);

        return $href || $url || $routeExists || ! $hideMissingRoutes;
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function target(array $item): ?string
    {
        if (isset($item['target'])) {
            return $item['target'];
        }

        return self::isExternal($item) ? '_blank' : null;
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function rel(array $item): ?string
    {
        if (isset($item['rel'])) {
            return $item['rel'];
        }

        return self::isExternal($item) ? 'noopener noreferrer' : null;
    }
}
