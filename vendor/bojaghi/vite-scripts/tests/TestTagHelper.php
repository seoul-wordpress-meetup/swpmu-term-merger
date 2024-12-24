<?php

namespace Bojaghi\ViteScripts\Tests;

use Bojaghi\ViteScripts\TagHelper;
use \WP_UnitTestCase;

class TestTagHelper extends WP_UnitTestCase
{
    /**
     * @see          TagHelper::replace()
     * @dataProvider replaceDataProvider
     */
    public function test_replace(string $expected, string $input): void
    {
        $this->assertEquals($expected, TagHelper::replace($input));
    }

    /**
     * @noinspection JSUnresolvedLibraryURL
     * @noinspection HtmlUnknownTarget
     */
    protected function replaceDataProvider(): array
    {
        return [
            'Sample #001' => [
                // Expected
                "<script id='test-handle' src='asset.min.js' type='module'></script>\n" .
                "<script id='test-handle-after-script'>const foo='bar';</script>\n" .
                "<script id='another-handle' type='module' src='another-asset.min.js'></script>",
                // Input
                "<script id='test-handle' src='asset.min.js'></script>" .
                "<script id='test-handle-after-script'>const foo='bar';</script>" .
                "<script id='another-handle' type='text/javascript' src='another-asset.min.js'></script>",
            ],
        ];
    }

    /**
     * @see          TagHelper::scriptTagSplit()
     * @dataProvider scriptTagSplitDataProvider
     */
    public function test_scriptTagSplit(array $expected, string $input): void
    {
        $this->assertEquals($expected, TagHelper::scriptTagSplit($input), 'Split failed for input: ' . $input);
    }

    /**
     * @noinspection JSUnresolvedLibraryURL
     */
    protected function scriptTagSplitDataProvider(): array
    {
        return [
            'Split #001' => [
                // Expected
                [
                    "<script  src='https://foo.com/assets/script.js'></script>",
                ],
                // Input
                "\n\n<script  src='https://foo.com/assets/script.js'></script>\n\n",
            ],
            'Split #002' => [
                // Expected
                [
                    '<script type="text/javascript" src="https://foo.com/assets/script.js"></script>',
                    '<p>Dummy Text</p>',
                    '<script>const x = "test code";</script>',
                    '<div>Foo Bar</div>',
                ],
                // Input
                '<script type="text/javascript" src="https://foo.com/assets/script.js"></script> <p>Dummy Text</p> <script>const x = "test code";</script> <div>Foo Bar</div>',
            ],
            'Split #003' => [
                // Expected
                [
                    '<script id="my-handle" src="https://foo.com/assets/script.js"></script>',
                    '<script id="my-handle-script">(function () {})();</script>',
                ],
                // Input
                '<script id="my-handle" src="https://foo.com/assets/script.js"></script> ' . PHP_EOL . PHP_EOL .
                '<script id="my-handle-script">(function () {})();</script>',
            ],
        ];
    }

    /**
     * @see          TagHelper::scriptAttrsExtract()
     * @dataProvider scriptAttrsExtractDataProvider
     */
    public function test_scriptAttrsExtract(array $expected, string $input): void
    {
        $this->assertEquals($expected, TagHelper::scriptAttrsExtract($input), 'Extract failed for input: ' . $input);
    }

    protected function scriptAttrsExtractDataProvider(): array
    {
        return [
            'Sample #001' => [
                // Expected
                [],
                // Input
                '<script ></script>'
            ],
            'Sample #002' => [
                // Expected
                [
                    'id'   => 'my-handle',
                    'type' => 'text/javascript',
                ],
                // Input
                '<script id="my-handle" type="text/javascript"></script>'
            ],
        ];
    }
}
