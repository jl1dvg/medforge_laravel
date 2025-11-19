<?php

namespace App\Services\Legacy;

use Illuminate\Support\Str;

class LegacyModuleRegistry
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $modules;

    public function __construct(private readonly array $config)
    {
        $this->modules = $this->normalizeModules($config['modules'] ?? []);
    }

    /**
     * Registered modules keyed by slug.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Locate a module by slug.
     */
    public function get(string $slug): ?array
    {
        $normalized = Str::slug($slug);

        return $this->modules[$normalized] ?? null;
    }

    /**
     * Determine if a module exists.
     */
    public function has(string $slug): bool
    {
        return $this->get($slug) !== null;
    }

    /**
     * Absolute path to the legacy controllers directory.
     */
    public function controllersPath(?string $append = null): string
    {
        return $this->resolvePath($this->config['controllers_path'] ?? base_path('controllers'), $append);
    }

    /**
     * Absolute path to the legacy modules directory.
     */
    public function modulesPath(?string $append = null): string
    {
        return $this->resolvePath($this->config['modules_path'] ?? base_path('modules'), $append);
    }

    /**
     * Absolute path to the legacy views directory.
     */
    public function viewsPath(?string $append = null): string
    {
        return $this->resolvePath($this->config['views_path'] ?? base_path('views'), $append);
    }

    /**
     * Absolute path to the public directory.
     */
    public function publicPath(?string $append = null): string
    {
        return $this->resolvePath($this->config['public_path'] ?? public_path(), $append);
    }

    /**
     * Absolute path to the public/assets directory.
     */
    public function publicAssetsPath(?string $append = null): string
    {
        return $this->resolvePath($this->config['public_assets_path'] ?? public_path('assets'), $append);
    }

    /**
     * Transform asset paths into filesystem metadata.
     *
     * @param array{styles: array<int, string>, scripts: array<int, string>} $assets
     * @return array{styles: array<int, array{relative: string, absolute: string}>, scripts: array<int, array{relative: string, absolute: string}>}
     */
    public function resolveAssetMetadata(array $assets): array
    {
        $resolved = ['styles' => [], 'scripts' => []];

        foreach ($assets as $type => $entries) {
            if (! isset($resolved[$type])) {
                continue;
            }

            foreach ($entries as $entry) {
                $relative = $this->normalizeSegment($entry);
                $resolved[$type][] = [
                    'relative' => $relative,
                    'absolute' => $this->publicPath($relative),
                ];
            }
        }

        return $resolved;
    }

    /**
     * @param array<string, array<string, mixed>> $modules
     * @return array<string, array<string, mixed>>
     */
    private function normalizeModules(array $modules): array
    {
        $normalized = [];

        foreach ($modules as $slug => $definition) {
            $normalizedSlug = Str::slug($definition['slug'] ?? (string) $slug);
            $normalized[$normalizedSlug] = [
                ...$definition,
                'slug' => $normalizedSlug,
                'enabled' => (bool) ($definition['enabled'] ?? true),
                'routes' => $this->normalizeRoutes($definition['routes'] ?? []),
                'assets' => $this->normalizeAssets($definition['assets'] ?? []),
                'views' => array_values($definition['views'] ?? []),
            ];
        }

        return array_filter(
            $normalized,
            fn (array $module): bool => $module['enabled'] === true
        );
    }

    /**
     * @param array<string, array<int, array{method: string, uri: string}>> $routes
     * @return array{web: array<int, array{method: string, uri: string}>, api: array<int, array{method: string, uri: string}>}
     */
    private function normalizeRoutes(array $routes): array
    {
        $defaults = ['web' => [], 'api' => []];

        foreach ($defaults as $channel => $_) {
            if (! isset($routes[$channel])) {
                $routes[$channel] = [];
            }
        }

        return $routes;
    }

    /**
     * @param array<string, array<int, string>> $assets
     * @return array{styles: array<int, string>, scripts: array<int, string>}
     */
    private function normalizeAssets(array $assets): array
    {
        return [
            'styles' => array_values($assets['styles'] ?? []),
            'scripts' => array_values($assets['scripts'] ?? []),
        ];
    }

    private function resolvePath(string $base, ?string $append): string
    {
        if ($append === null || $append === '') {
            return $base;
        }

        return rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->normalizeSegment($append);
    }

    private function normalizeSegment(string $segment): string
    {
        return ltrim($segment, DIRECTORY_SEPARATOR.' ');
    }
}
