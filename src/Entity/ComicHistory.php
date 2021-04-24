<?php

namespace App\Entity;

use App\Repository\ComicHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Comic;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass=ComicHistoryRepository::class)
 */
class ComicHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comicHistories")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comic", inversedBy="comicHistories")
     */
    protected $comic;

    /**
     * @ORM\Column(type="integer")
     */
    private $page;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComic() {
        return $this->comic;
    }

    public function setComic(Comic $comic) {
        $this->comic = $comic;
        return $this;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage($page) {
        $this->page = $page;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
}
