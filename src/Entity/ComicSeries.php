<?php

namespace App\Entity;

use App\Repository\ComicSeriesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Comic;

/**
 * @ORM\Entity(repositoryClass=ComicSeriesRepository::class)
 */
class ComicSeries
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $startYear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endYear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $match;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apiDetailUrl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comic", mappedBy="series", cascade={"persist"}, orphanRemoval = true)
     */
    protected $comics;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getStartYear(): ?string
    {
        return $this->startYear;
    }

    public function setStartYear(?string $startYear): self
    {
        $this->startYear = $startYear;

        return $this;
    }

    public function getEndYear(): ?string
    {
        return $this->endYear;
    }

    public function setEndYear(?string $endYear): self
    {
        $this->endYear = $endYear;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMatch()
    {
        return $this->match;
    }

    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    public function getApiDetailUrl(): ?string
    {
        return $this->apiDetailUrl;
    }

    public function setApiDetailUrl(?string $apiDetailUrl): self
    {
        $this->apiDetailUrl = $apiDetailUrl;

        return $this;
    }

    public function getComics() {
        return $this->comics;
    }

    public function addComic(Comic $comic)
    {
        $comic->setSeries($this);
        $this->comics->add($comic);
    }

    public function removeComic(Comic $comic)
    {
        $this->comics->removeElement($comic);
        $comic->setSeries(null);
    }
}
