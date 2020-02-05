<?php

namespace App\Entity\Parametres\UsersGroups;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Parametres\UsersGroups\UsersRepository")
 * @Vich\Uploadable
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100 ,  nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ddn;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tel_interne;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tel_portable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creation_date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parametres\UsersGroups\Services", inversedBy="users")
     * 
     */
    private $service;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Parametres\UsersGroups\Groups", inversedBy="users")
     */
    private $Groupes;


    public function __construct()
    {
        $this->Groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDdn(): ?\DateTimeInterface
    {
        return $this->ddn;
    }

    public function setDdn(?\DateTimeInterface $ddn): self
    {
        $this->ddn = $ddn;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelInterne(): ?int
    {
        return $this->tel_interne;
    }

    public function setTelInterne(?int $tel_interne): self
    {
        $this->tel_interne = $tel_interne;

        return $this;
    }

    public function getTelPortable(): ?int
    {
        return $this->tel_portable;
    }

    public function setTelPortable(?int $tel_portable): self
    {
        $this->tel_portable = $tel_portable;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getLastLoginDate(): ?\DateTimeInterface
    {
        return $this->last_login_date;
    }

    public function setLastLoginDate(?\DateTimeInterface $last_login_date): self
    {
        $this->last_login_date = $last_login_date;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(?\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection|Groups[]
     */
    public function getGroupes(): Collection
    {
        return $this->Groupes;
    }

    public function addGroupe(Groups $groupe): self
    {
        if (!$this->Groupes->contains($groupe)) {
            $this->Groupes[] = $groupe;
        }

        return $this;
    }

    public function removeGroupe(Groups $groupe): self
    {
        if ($this->Groupes->contains($groupe)) {
            $this->Groupes->removeElement($groupe);
        }

        return $this;
    }

    public function eraseCredentials(): void
    {
        
    }
    public function getSalt(): ?string
    {
        return null;
    }
    public function getRoles()
    {
        $roles = array();
        $roles[] = "ROLE_USER";
        foreach ($this->Groupes as $role) {
            $roles[] = $role->getRole();
        }
            return $roles;
    }

}
