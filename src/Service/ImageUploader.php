<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ImageUploadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploader
{
    private const MAX_SIZE_BYTES = 5_242_880;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
    ];

    private const REQUIRED_DIRECTORIES = [
        'artists/profile',
        'artists/profile/thumbs',
        'artists/covers',
        'professionals/logo',
        'professionals/logo/thumbs',
        'professionals/covers',
    ];

    private readonly Filesystem $filesystem;

    public function __construct(
        private readonly ImageProcessor $imageProcessor,
        private readonly string $uploadDirectory,
    ) {
        $this->filesystem = new Filesystem();
        $this->ensureDirectoriesExist();
    }

    public function upload(UploadedFile $file, ImageUploadPreset $preset, ?string $existingRelativePath = null): ImageUploadResult
    {
        $this->validateUploadedFile($file);

        if ($existingRelativePath !== null) {
            $this->deleteImage($existingRelativePath, $preset);
        }

        $filename = $this->generateFilename();
        $mainRelativePath = $preset->directory().'/'.$filename;
        $mainAbsolutePath = $this->resolveAbsolutePath($mainRelativePath);

        $dimensions = $preset->mainDimensions();
        $this->imageProcessor->process(
            $file->getPathname(),
            $mainAbsolutePath,
            $dimensions['width'],
            $dimensions['height'],
        );

        $thumbRelativePath = null;

        if ($preset->hasThumb()) {
            $thumbDimensions = $preset->thumbDimensions();
            $thumbRelativePath = $preset->thumbDirectory().'/'.$filename;
            $thumbAbsolutePath = $this->resolveAbsolutePath($thumbRelativePath);

            $this->imageProcessor->process(
                $file->getPathname(),
                $thumbAbsolutePath,
                $thumbDimensions['width'],
                $thumbDimensions['height'],
            );
        }

        return new ImageUploadResult($mainRelativePath, $thumbRelativePath);
    }

    public function getThumbPath(?string $mainRelativePath): ?string
    {
        if ($mainRelativePath === null || $mainRelativePath === '') {
            return null;
        }

        $directory = \dirname($mainRelativePath);

        return $directory.'/thumbs/'.\basename($mainRelativePath);
    }

    public function deleteImage(?string $relativePath, ImageUploadPreset $preset): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $mainPath = $this->resolveAbsolutePath($relativePath);
        if ($this->filesystem->exists($mainPath)) {
            $this->filesystem->remove($mainPath);
        }

        if ($preset->hasThumb()) {
            $thumbRelativePath = $this->getThumbPath($relativePath);
            if ($thumbRelativePath !== null) {
                $thumbPath = $this->resolveAbsolutePath($thumbRelativePath);
                if ($this->filesystem->exists($thumbPath)) {
                    $this->filesystem->remove($thumbPath);
                }
            }
        }
    }

    private function validateUploadedFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new ImageUploadException('Le fichier envoyé est invalide.');
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new ImageUploadException('L\'image ne doit pas dépasser 5 Mo.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!\in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new ImageUploadException('Format non autorisé. Formats acceptés : JPG, JPEG, PNG, WebP.');
        }

        $guessedExtension = strtolower((string) $file->guessExtension());
        if ($guessedExtension !== '' && !\in_array($guessedExtension, self::ALLOWED_EXTENSIONS, true)) {
            throw new ImageUploadException('Format non autorisé. Formats acceptés : JPG, JPEG, PNG, WebP.');
        }

        $clientMimeType = $file->getMimeType();
        if ($clientMimeType !== null && !\in_array($clientMimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new ImageUploadException('Type MIME non autorisé.');
        }

        $detectedMimeType = (new \finfo(\FILEINFO_MIME_TYPE))->file($file->getPathname());
        if ($detectedMimeType === false || !\in_array($detectedMimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new ImageUploadException('Le contenu du fichier n\'est pas une image valide.');
        }
    }

    private function ensureDirectoriesExist(): void
    {
        foreach (self::REQUIRED_DIRECTORIES as $directory) {
            $absolutePath = $this->uploadDirectory.'/'.$directory;
            if (!is_dir($absolutePath)) {
                mkdir($absolutePath, 0775, true);
            }
        }
    }

    private function generateFilename(): string
    {
        return bin2hex(random_bytes(16)).'.webp';
    }

    private function resolveAbsolutePath(string $relativePath): string
    {
        return $this->uploadDirectory.'/'.\ltrim($relativePath, '/');
    }
}
