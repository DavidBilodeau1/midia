<?php

namespace App\Entity;

use App\Repository\ComicRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ComicHistory;

/**
 * @ORM\Entity(repositoryClass=ComicRepository::class)
 */
class Comic
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $volume;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $absolutePath;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalPages;


    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $match;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apiDetailUrl;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ComicSeries", inversedBy="comics")
     */
    protected $series;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComicHistory", mappedBy="comic", cascade={"persist"}, orphanRemoval = true)
     */
    protected $comicHistories;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTotalPages()
    {
        return $this->totalPages;
    }

    public function setTotalPages($totalPages): self
    {
        $this->totalPages = $totalPages;

        return $this;
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

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(?string $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

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

    public function getAbsolutePath(): ?string
    {
        return $this->absolutePath;
    }

    public function setAbsolutePath(?string $path): self
    {
        $this->absolutePath = $path;

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


    public function getMatch()
    {
        return $this->match;
    }

    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    public function getSeries()
    {
        return $this->series;
    }

    public function setSeries($series)
    {
        $this->series = $series;

        return $this;
    }

    public function getComicHistories() {
        return $this->comicHistories;
    }

    public function addComicHistory(ComicHistory $comicHistory)
    {
        $comicHistories->setComic($this);
        $this->comicHistories->add($comicHistory);
    }

    public function removeComicHistory(ComicHistory $comicHistory)
    {
        $this->comicHistories->removeElement($comicHistory);
        $comicHistories->setComic(null);
    }
}
