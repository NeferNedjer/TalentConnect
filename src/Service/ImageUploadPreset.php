<?php

declare(strict_types=1);

namespace App\Service;

enum ImageUploadPreset: string
{
    case ArtistProfile = 'artist_profile';
    case ArtistCover = 'artist_cover';
    case ProfessionalLogo = 'professional_logo';
    case ProfessionalCover = 'professional_cover';

    public function directory(): string
    {
        return match ($this) {
            self::ArtistProfile => 'artists/profile',
            self::ArtistCover => 'artists/covers',
            self::ProfessionalLogo => 'professionals/logo',
            self::ProfessionalCover => 'professionals/covers',
        };
    }

    public function thumbDirectory(): ?string
    {
        return match ($this) {
            self::ArtistProfile => 'artists/profile/thumbs',
            self::ProfessionalLogo => 'professionals/logo/thumbs',
            default => null,
        };
    }

    /**
     * @return array{width: int, height: int}
     */
    public function mainDimensions(): array
    {
        return match ($this) {
            self::ArtistProfile, self::ProfessionalLogo => ['width' => 512, 'height' => 512],
            self::ArtistCover, self::ProfessionalCover => ['width' => 1600, 'height' => 600],
        };
    }

    /**
     * @return array{width: int, height: int}|null
     */
    public function thumbDimensions(): ?array
    {
        return match ($this) {
            self::ArtistProfile, self::ProfessionalLogo => ['width' => 160, 'height' => 160],
            default => null,
        };
    }

    public function hasThumb(): bool
    {
        return $this->thumbDirectory() !== null;
    }
}
