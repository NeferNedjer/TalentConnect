<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

final class SlugGenerator
{
    public function __construct(
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function generate(string $text): string
    {
        return $this->slugger->slug($text)->lower()->toString();
    }
}
