<?php

namespace App\Tests\Service\YoutubeDl;

use App\Service\YoutubeDl\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    /**
     * @dataProvider createFromOutputProvider
     */
    public function testCreateFromOutput(
        string $output,
        string $code,
        string $extension,
        string $resolution,
        string $note,
        bool $isAudioOnly
    ): void {
        $format = Format::createFromOutput($output);

        $this->assertEquals($code, $format->getCode());
        $this->assertEquals($extension, $format->getExtension());
        $this->assertEquals($resolution, $format->getResolution());
        $this->assertEquals($note, $format->getNote());
        $this->assertEquals($isAudioOnly, $format->isAudioOnly());
    }

    /**
     * @return array<array<mixed>>
     */
    public function createFromOutputProvider(): array
    {
        return [
            [
                '249          webm       audio only tiny   73k , opus @ 50k (48000Hz), 83.98MiB',
                '249',
                'webm',
                'audio only',
                'tiny   73k , opus @ 50k (48000Hz), 83.98MiB',
                true,
            ],
            [
                '140          m4a        audio only tiny  142k , m4a_dash container, mp4a.40.2@128k (44100Hz), 195.68MiB',
                '140',
                'm4a',
                'audio only',
                'tiny  142k , m4a_dash container, mp4a.40.2@128k (44100Hz), 195.68MiB',
                true,
            ],
            [
                '242          webm       426x240    240p  125k , vp9, 30fps, video only, 81.04MiB',
                '242',
                'webm',
                '426x240',
                '240p  125k , vp9, 30fps, video only, 81.04MiB',
                false,
            ],
            [
                '137          mp4        1920x1080  1080p  383k , avc1.640028, 30fps, video only, 413.57MiB',
                '137',
                'mp4',
                '1920x1080',
                '1080p  383k , avc1.640028, 30fps, video only, 413.57MiB',
                false,
            ],
        ];
    }
}
