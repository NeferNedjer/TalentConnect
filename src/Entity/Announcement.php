<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AnnouncementStatus;
use App\Enum\AnnouncementType;
use App\Enum\RemunerationType;
use App\Validator\Constraints\ValidAnnouncementPublisher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'announcement')]
#[ORM\Index(name: 'IDX_ANNOUNCEMENT_STATUS', fields: ['status'])]
#[ORM\Index(name: 'IDX_ANNOUNCEMENT_PUBLISHED_AT', fields: ['publishedAt'])]
#[ORM\Index(name: 'IDX_ANNOUNCEMENT_CITY', fields: ['city'])]
#[ValidAnnouncementPublisher]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $title;

    #[ORM\Column(length: 150, unique: true)]
    private string $slug;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(enumType: AnnouncementType::class)]
    private AnnouncementType $announcementType;

    #[ORM\Column(enumType: AnnouncementStatus::class)]
    private AnnouncementStatus $status = AnnouncementStatus::Draft;

    #[ORM\Column(enumType: RemunerationType::class)]
    private RemunerationType $remunerationType;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $remunerationAmount = null;

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $country = null;

    #[ORM\Column]
    private bool $isRemote = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\ManyToOne(targetEntity: ArtistProfile::class, inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: true)]
    private ?ArtistProfile $publisherArtist = null;

    #[ORM\ManyToOne(targetEntity: ProfessionalProfile::class, inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: true)]
    private ?ProfessionalProfile $publisherProfessional = null;

    /**
     * @var Collection<int, Genre>
     */
    #[ORM\ManyToMany(targetEntity: Genre::class)]
    #[ORM\JoinTable(name: 'announcement_genre')]
    private Collection $genres;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAnnouncementType(): AnnouncementType
    {
        return $this->announcementType;
    }

    public function setAnnouncementType(AnnouncementType $announcementType): static
    {
        $this->announcementType = $announcementType;

        return $this;
    }

    public function getStatus(): AnnouncementStatus
    {
        return $this->status;
    }

    public function setStatus(AnnouncementStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRemunerationType(): RemunerationType
    {
        return $this->remunerationType;
    }

    public function setRemunerationType(RemunerationType $remunerationType): static
    {
        $this->remunerationType = $remunerationType;

        return $this;
    }

    public function getRemunerationAmount(): ?string
    {
        return $this->remunerationAmount;
    }

    public function setRemunerationAmount(?string $remunerationAmount): static
    {
        $this->remunerationAmount = $remunerationAmount;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function isRemote(): bool
    {
        return $this->isRemote;
    }

    public function setIsRemote(bool $isRemote): static
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getPublisherArtist(): ?ArtistProfile
    {
        return $this->publisherArtist;
    }

    public function setPublisherArtist(?ArtistProfile $publisherArtist): static
    {
        if ($this->publisherArtist === $publisherArtist) {
            return $this;
        }

        if ($publisherArtist !== null) {
            $this->setPublisherProfessional(null);
        }

        if ($this->publisherArtist !== null) {
            $this->publisherArtist->removeAnnouncement($this);
        }

        $this->publisherArtist = $publisherArtist;

        if ($publisherArtist !== null) {
            $publisherArtist->addAnnouncement($this);
        }

        return $this;
    }

    public function getPublisherProfessional(): ?ProfessionalProfile
    {
        return $this->publisherProfessional;
    }

    public function setPublisherProfessional(?ProfessionalProfile $publisherProfessional): static
    {
        if ($this->publisherProfessional === $publisherProfessional) {
            return $this;
        }

        if ($publisherProfessional !== null) {
            $this->setPublisherArtist(null);
        }

        if ($this->publisherProfessional !== null) {
            $this->publisherProfessional->removeAnnouncement($this);
        }

        $this->publisherProfessional = $publisherProfessional;

        if ($publisherProfessional !== null) {
            $publisherProfessional->addAnnouncement($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): static
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        $this->genres->removeElement($genre);

        return $this;
    }
}
