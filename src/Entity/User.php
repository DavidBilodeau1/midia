<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\ComicHistory;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $status;

    /**
    * Date/Time of the last activity
    *
    * @var \Datetime
    * @ORM\Column(name="last_activity_at", type="datetime")
    */
    protected $lastActivityAt;

    /**
    * string of current url
    *
    * @var string
    * @ORM\Column(name="current_path", type="string", length=255)
    */
    protected $currentPath;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComicHistory", mappedBy="user", cascade={"persist"}, orphanRemoval = true)
     */
    protected $comicHistories;

    /**
    * @param \Datetime $lastActivityAt
    */
    public function setLastActivityAt($lastActivityAt)
    {
        $this->lastActivityAt = $lastActivityAt;
    }

    /**
    * @return \Datetime
    */
    public function getLastActivityAt()
    {
        return $this->lastActivityAt;
    }

    /**
    * @param string $currentPath
    */
    public function setCurrentPath($currentPath)
    {
        $this->currentPath = $currentPath;
    }

    /**
    * @return string $currentPath
    */
    public function getCurrentPath()
    {
        return $this->currentPath;
    }

    /**
    * @return Bool Whether the user is active or not
    */
    public function isActiveNow()
    {
    // Delay during wich the user will be considered as still active
        $delay = new \DateTime('5 minutes ago');
        if( $this->getLastActivityAt() > $delay )
        {
            $this->setStatus('online');
        }

        return ( $this->getLastActivityAt() > $delay );
    }

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of Status
     *
     * @param mixed $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getComicHistories() {
        return $this->comicHistories;
    }

    public function addComicHistory(ComicHistory $comicHistory)
    {
        $comicHistories->setUser($this);
        $this->comicHistories->add($comicHistory);
    }

    public function removeComicHistory(ComicHistory $comicHistory)
    {
        $this->comicHistories->removeElement($comicHistory);
        $comicHistories->setUser(null);
    }
}
