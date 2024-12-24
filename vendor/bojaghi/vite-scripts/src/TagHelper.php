<?php

namespace SWM\TermMerger\Vendor\Bojaghi\ViteScripts;

class TagHelper
{
    public static function replace(string $input): string
    {
        $buffer = [];
        $lines  = self::scriptTagSplit($input);

        foreach ($lines as $line) {
            if (
                str_starts_with($line, '<script') &&
                ($pos = strpos($line, '>')) !== false &&
                ($attrs = self::scriptAttrsExtract($line)) &&
                (isset($attrs['src']) && (!isset($attrs['type']) || $attrs['type'] !== 'module'))
            ) {
                $attrs['type'] = 'module';
                $buffer[]      =
                    '<script ' . implode(
                        separator: ' ',
                        array: array_map(
                            fn($key, $value) => "$key='$value'",
                            array_keys($attrs),
                            array_values($attrs),
                        ),
                    ) . '>' . substr($line, $pos + 1);
            } else {
                $buffer[] = $line;
            }
        }

        return implode("\n", $buffer);
    }

    public static function scriptTagSplit(string $input): array
    {
        $split = preg_split(';(<script(?>\s[^>]+)?>.*?</script>);', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        return is_array($split) ? array_values(array_filter(array_map('trim', $split))) : [];
    }

    public static function scriptAttrsExtract(string $input): array
    {
        preg_match_all('/(\w+)=["\']?((?:.(?!["\']?\s+\S+=|\s*\/?[>"\']))+.)["\']?/', $input, $matches, PREG_SET_ORDER);

        $output = [];

        if ($matches) {
            foreach ($matches as $match) {
                $output[$match[1]] = $match[2];
            }
        }

        return $output;
    }
}
