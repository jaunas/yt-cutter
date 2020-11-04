<?php

namespace App\Service\YoutubeDl;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class Installer
{
    const DOWNLOAD_URL = 'https://yt-dl.org/downloads/latest/youtube-dl';
    const VERSION_URL = 'https://yt-dl.org/downloads/latest';

    /** @var string */
    private $execPath;

    public function __construct(KernelInterface $kernel)
    {
        $this->execPath = $kernel->getProjectDir() . '/youtube-dl';
    }

    public function isInstalled(): bool
    {
        $fileSystem = new Filesystem();

        return $fileSystem->exists($this->execPath);
    }

    public function install(): void
    {
        file_put_contents($this->execPath, file_get_contents(self::DOWNLOAD_URL));

        $fileSystem = new Filesystem();
        $fileSystem->chmod($this->execPath, 0775);
    }

    public function version(): string
    {
        $process = new Process([$this->execPath, '--version']);
        $process->mustRun();

        return trim($process->getOutput());
    }

    public function availableVersion(): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::VERSION_URL);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
        $url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        $parts = explode('/', $url);

        return end($parts);
    }
}
