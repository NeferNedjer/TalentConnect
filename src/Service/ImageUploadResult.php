<?php

declare(strict_types=1);

namespace App\Service;

final readonly class ImageUploadResult
{
    public function __construct(
        private string $mainPath,
        private ?string $thumbPath = null,
    ) {
    }

    public function getMainPath(): string
    {
        return $this->mainPath;
    }

    public function getThumbPath(): ?string
    {
        return $this->thumbPath;
    }
}
