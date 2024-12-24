<?php

namespace SWM\TermMerger\Vendor\Bojaghi\ViteScripts;

use SWM\TermMerger\Vendor\Bojaghi\Helper\Helper;
use InvalidArgumentException;

class ViteScript
{
    private static bool $footerScriptFlag;

    private string $devServerUrl;
    private string $distBaseUrl;
    private bool $isProd;
    private string $manifestPath;
    private ?ViteManifest $manifest;
    private array $handles;
    private array $modulePreloads;

    public function __construct(array|string $args = [])
    {
        self::$footerScriptFlag = false;

        $defaults = [
            'devServerUrl' => 'http://localhost:5173', // Optional
            'distBaseUrl'  => '',                      // Required
            'isProd'       => true,                    // Optional
            'manifestPath' => '',                      // Required
        ];

        // Required field check.
        $args = wp_parse_args(Helper::loadConfig($args), $defaults);
        $keys = ['distBaseUrl', 'manifestPath'];
        foreach ($keys as $key) {
            if (empty($args[$key])) {
                throw new InvalidArgumentException("\$args['$key'] is required");
            }
        }

        $this->devServerUrl   = rtrim((string)$args['devServerUrl'], '/') ?: $defaults['devServerUrl'];
        $this->distBaseUrl    = rtrim((string)$args['distBaseUrl'], '/');
        $this->isProd         = (bool)$args['isProd'];
        $this->manifestPath   = (string)$args['manifestPath'];
        $this->manifest       = null;
        $this->handles        = [];
        $this->modulePreloads = [];

        if ($this->isDevelopment()) {
            wp_register_script(
                '@vite-client',
                "$this->devServerUrl/@vite/client",
                [],
                null,
                [
                    'in_footer' => true,
                    'strategy'  => 'defer',
                ],
            );
            $this->handles['@vite-client'] = true;
        }

        add_filter('script_loader_tag', [$this, 'overrideScriptType'], 9999, 3);
    }

    public function add(string $handle, string $relPath, array $extraDeps = []): ?Localize
    {
        if (wp_script_is($handle)) {
            return null;
        }

        $this->handles[$handle] = true;

        if ($this->isProduction()) {
            $this->prodEnqueue($handle, $relPath, $extraDeps);
        } else {
            $this->devEnqueue($handle, $relPath, $extraDeps);
        }

        return Localize::create($this, $handle);
    }

    protected function devEnqueue(string $handle, string $relPath, array $extraDeps): void
    {
        if (!self::$footerScriptFlag) {
            self::$footerScriptFlag = true;
            add_action('admin_print_footer_scripts', [$this, 'printRefreshScript'], 9);
            add_action('wp_print_footer_scripts', [$this, 'printRefreshScript'], 9);
        }

        wp_enqueue_script(
            $handle,
            "$this->devServerUrl/$relPath",
            [
                'wp-i18n',
                '@vite-client',
                ...$extraDeps,
            ],
            null,
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ],
        );

        wp_add_inline_script(
            $handle,
            "console.info('$relPath is running in development mode.');",
        );
    }

    protected function prodEnqueue(string $handle, string $relPath, array $extraDeps): void
    {
        if (!self::$footerScriptFlag) {
            self::$footerScriptFlag = true;
            add_action('admin_print_footer_scripts', [$this, 'printModulePreloads'], 11);
            add_action('wp_print_footer_scripts', [$this, 'printModulePreloads'], 11);
        }

        if (!$this->manifest) {
            $this->manifest = new ViteManifest($this->manifestPath);
        }

        $chunks = $this->manifest->getChunks($relPath, $this->distBaseUrl);

        if (!$chunks['script']) {
            return;
        }

        wp_enqueue_script(
            handle: $handle,
            src: $chunks['script'],
            deps: $extraDeps,
            ver: null,
            args: [
                'strategy'  => 'defer',
                'in_footer' => true,
            ],
        );

        foreach ($chunks['styles'] as $idx => $style) {
            wp_enqueue_style(
                "$handle-$idx",
                src: $style,
                ver: null,
            );
        }

        if ($chunks['imports']) {
            $this->modulePreloads = [...$this->modulePreloads, ...$chunks['imports']];
        }
    }

    public function remove(string $handle): void
    {
        if (wp_script_is($handle)) {
            wp_dequeue_script($handle);
            unset($this->handles[$handle]);
        }
    }

    public function isProduction(): bool
    {
        return $this->isProd;
    }

    public function isDevelopment(): bool
    {
        return !$this->isProduction();
    }

    public function overrideScriptType(string $tag, string $handle): string
    {
        if (isset($this->handles[$handle])) {
            $tag = TagHelper::replace($tag);
        }

        return $tag;
    }

    public function printRefreshScript(): void
    {
        // @formatter:off ?>

<script id="vite-script-refresh" type="module">
    import RefreshRuntime from '<?php echo esc_html($this->devServerUrl); ?>/@react-refresh'
    RefreshRuntime.injectIntoGlobalHook(window)
    window.$RefreshReg$ = () => {}
    window.$RefreshSig$ = () => (type) => type
    window.__vite_plugin_react_preamble_installed__ = true
</script>

    <?php // @formatter:on
    }

    public function printModulePreloads(): void
    {
        foreach ($this->modulePreloads as $item) {
            echo '<link rel="modulepreload" href="' . esc_url($item) . '/>' . PHP_EOL;
        }
    }
}
