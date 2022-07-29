<?php

namespace Phile\Plugin\Phile\ParserMeta\Tests;

use Phile\Plugin\Phile\ParserMeta\Parser\Meta;
use Phile\Test\TestCase;

class MetaTest extends TestCase
{
    public function testYamlWithYamlFrontMatter()
    {
        $this->createPhileCore()->bootstrap();

        $raw = <<<EOF
---
Title: foo 
Tags: [bar, baz]
---

Page Content
EOF;

        $parser = new Meta([
            'fences' => ['yaml' => ['open' => '---', 'close' => '---']],
            'format' => 'YAML'
        ]);
        $meta = $parser->extractMeta($raw);
        $this->assertSame('foo', $meta['title']);
        $this->assertSame(['bar', 'baz'], $meta['tags']);
    }

    public function testDataWithoutMetaDataBlock()
    {
        $raw = "# Hello World\n## Hello you too";
        $parser = new Meta([
            'fences' => ['yaml' => ['open' => '---', 'close' => '---']],
        ]);

        $content = $parser->extractContent($raw);
        $this->assertSame($raw, $content);

        $meta = $parser->extractMeta($raw);
        $this->assertSame([], $meta);
    }
}
