<?php

namespace App\Service\YoutubeDl;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class Downloader
{
    /** @var string */
    private $execPath;

    public function __construct(KernelInterface $kernel)
    {
        $this->execPath = $kernel->getProjectDir() . '/youtube-dl';
    }

    private function getDescription(string $url): string
    {
        $process = new Process([$this->execPath, $url, '--get-description']);
        $process->mustRun();

        return $process->getOutput();
    }

    private function getTracks(string $description): array
    {
        preg_match_all('/^(\d+:\d{2}(?>:\d{2})?)\W*(.*)$/m', $description, $matches);

        $tracks = [];
        foreach ($matches[0] as $index => $match) {
            $tracks[] = [
                'time' => $matches[1][$index],
                'title' => $matches[2][$index]
            ];
        }

        return $tracks;
    }

    public function getTracksFromUrl(string $url): array
    {
        return $this->getTracks($this->getDescription($url));
    }

    /**
     * @return Format[]
     */
    public function getFormats(string $url): array
    {
        $process = new Process([$this->execPath, $url, '--list-formats']);
        $process->mustRun();

        $output = explode("\n", $process->getOutput());
        $output = array_slice($output, 3);
        $output = array_filter($output, function ($line) {
            return !empty($line);
        });

        return array_map(function($line) {
            return Format::createFromOutput($line);
        }, $output);
    }
}
