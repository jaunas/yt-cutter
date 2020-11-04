<?php

namespace App\Service\YoutubeDl;

class Format
{
    /** @var string */
    private $code;

    /** @var string */
    private $extension;

    /** @var string */
    private $resolution;

    /** @var string */
    private $note;

    public function __construct(string $code, string $extension, string $resolution, string $note)
    {
        $this->code = $code;
        $this->extension = $extension;
        $this->resolution = $resolution;
        $this->note = $note;
    }

    static public function createFromOutput(string $output)
    {
        $code = rtrim(substr($output, 0, 12));
        $extension = rtrim(substr($output, 13, 10));
        $resolution = rtrim(substr($output, 24, 10));
        $note = substr($output, 35);

        return new self($code, $extension, $resolution, $note);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getResolution(): string
    {
        return $this->resolution;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function getDescription(): string
    {
        return sprintf('.%s; %s; %s', $this->extension, $this->resolution, $this->note);
    }

    public function isAudioOnly(): bool
    {
        return 'audio only' == $this->resolution;
    }
}
