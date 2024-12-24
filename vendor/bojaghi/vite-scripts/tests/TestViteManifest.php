<?php

namespace Bojaghi\ViteScripts\Tests;

use Bojaghi\ViteScripts\ViteManifest;
use \WP_UnitTestCase;

class TestViteManifest extends WP_UnitTestCase
{
    /**
     * @dataProvider getChunksProvider
     *
     * @param string $manifestFile
     * @param array  $expected
     * @param string $input
     *
     * @return void
     */
    public function test_getChunks(string $manifestFile, array $expected, string $input): void
    {
        $manifest = new ViteManifest($manifestFile);
        $actual   = $manifest->getChunks($input, '');

        $this->assertEquals($expected, $actual);
    }

    protected function getChunksProvider(): array
    {
        return [
            'manifest-sample.json views/foo.js' => [
                'file'     => __DIR__ . '/sample-data/manifest-sample.json',
                'expected' => [
                    'script'  => '/assets/foo-BRBmoGS9.js',
                    'styles'  => [
                        '/assets/foo-5UjPuW-k.css',
                        '/assets/shared-ChJ_j-JJ.css',
                    ],
                    'imports' => ['/assets/shared-B7PI925R.js'],
                ],
                'input'    => 'views/foo.js',
            ],
            'manifest-sample.json views/bar.js' => [
                'file'     => __DIR__ . '/sample-data/manifest-sample.json',
                'expected' => [
                    'script'  => '/assets/bar-gkvgaI9m.js',
                    'styles'  => ['/assets/shared-ChJ_j-JJ.css'],
                    'imports' => ['/assets/shared-B7PI925R.js'],
                ],
                'input'    => 'views/bar.js',
            ],
        ];
    }
}
