<?php

namespace SWM\TermMerger\Vendor\Bojaghi\ViteScripts;

class ViteManifest
{
    private array $manifest;

    public function __construct(string|array $manifest)
    {
        if (is_string($manifest)) {
            $this->manifest = json_decode(file_get_contents($manifest) ?: '[]', true) ?: [];
        } else {
            $this->manifest = $manifest;
        }
    }

    /**
     * @param string $relPath
     * @param string $baseUrl
     *
     * @return array{script: string, styles: string[], imports: string[]}
     */
    public function getChunks(string $relPath, string $baseUrl): array
    {
        $key     = $relPath;
        $baseUrl = trailingslashit($baseUrl);

        $script  = $this->manifest[$key]['file'] ?? '';
        $styles  = $this->manifest[$key]['css'] ?? [];
        $imports = $this->manifest[$key]['imports'] ?? [];

        reset($imports);

        while (($import = current($imports))) {
            $css = $this->manifest[$import]['css'] ?? [];
            if ($css) {
                $styles = [...$styles, ...$css];
            }
            $nestedImports = $this->manifest[$import]['imports'] ?? [];
            foreach ($nestedImports as $nestedImport) {
                $imports[] = $nestedImport;
            }

            next($imports);
        }

        return [
            'script'  => $baseUrl . $script,
            'styles'  => array_map(fn($p) => $baseUrl . $p, $styles),
            'imports' => array_map(fn($i) => $baseUrl . $this->manifest[$i]['file'], $imports),
        ];
    }
}
