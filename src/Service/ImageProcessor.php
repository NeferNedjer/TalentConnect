<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ImageUploadException;

final class ImageProcessor
{
    private const WEBP_QUALITY = 82;

    private readonly bool $useImagick;

    public function __construct()
    {
        $this->useImagick = \extension_loaded('imagick') && class_exists(\Imagick::class);
    }

    public function usesImagick(): bool
    {
        return $this->useImagick;
    }

    public function process(string $sourcePath, string $destinationPath, int $width, int $height): void
    {
        if ($this->useImagick) {
            $this->processWithImagick($sourcePath, $destinationPath, $width, $height);

            return;
        }

        if (!\extension_loaded('gd')) {
            throw new ImageUploadException('Aucune extension de traitement d\'image disponible (Imagick ou GD requis).');
        }

        $this->processWithGd($sourcePath, $destinationPath, $width, $height);
    }

    private function processWithImagick(string $sourcePath, string $destinationPath, int $width, int $height): void
    {
        $image = new \Imagick($sourcePath);
        $image->autoOrient();
        $image->stripImage();
        $image->cropThumbnailImage($width, $height);
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(self::WEBP_QUALITY);
        $image->writeImage($destinationPath);
        $image->clear();
        $image->destroy();
    }

    private function processWithGd(string $sourcePath, string $destinationPath, int $width, int $height): void
    {
        $source = $this->createGdImage($sourcePath);
        if ($source === false) {
            throw new ImageUploadException('Impossible de lire l\'image source.');
        }

        $processed = $this->cropAndResizeGd($source, $width, $height);
        imagedestroy($source);

        $directory = \dirname($destinationPath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            imagedestroy($processed);
            throw new ImageUploadException('Impossible de créer le dossier de destination.');
        }

        if (!imagewebp($processed, $destinationPath, self::WEBP_QUALITY)) {
            imagedestroy($processed);
            throw new ImageUploadException('Impossible d\'enregistrer l\'image WebP.');
        }

        imagedestroy($processed);
    }

    /**
     * @return \GdImage|false
     */
    private function createGdImage(string $path): \GdImage|false
    {
        $mime = (new \finfo(\FILEINFO_MIME_TYPE))->file($path);

        return match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default => false,
        };
    }

    private function cropAndResizeGd(\GdImage $source, int $targetWidth, int $targetHeight): \GdImage
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) round(($sourceHeight - $cropHeight) / 2);
        }

        $cropped = imagecrop($source, [
            'x' => $cropX,
            'y' => $cropY,
            'width' => $cropWidth,
            'height' => $cropHeight,
        ]);

        if ($cropped === false) {
            throw new ImageUploadException('Impossible de recadrer l\'image.');
        }

        $resized = imagescale($cropped, $targetWidth, $targetHeight, IMG_BILINEAR_FIXED);
        imagedestroy($cropped);

        if ($resized === false) {
            throw new ImageUploadException('Impossible de redimensionner l\'image.');
        }

        return $resized;
    }
}
