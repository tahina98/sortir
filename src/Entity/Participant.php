<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà pris l\'ami !')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Le champ nom ne doit pas être vide')]
    #[Assert\Regex('/^[^0-9]*$/', message: 'votre nom ne peut contenir que des lettres')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex('/^[^0-9]*$/', message: 'votre prénom ne peut contenir que des lettres')]
    private ?string $prenom = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Le champ pseudo ne peut pas être nul')]
    #[Assert\Length(null, 4)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Le champ téléphone ne doit pas être vide')]
    #[Assert\Regex('/^[0-9]{10}$/', message: 'Merci de renseigner un numéro de téléphone valide')]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Le champ mail ne doit pas être vide')]
    #[Assert\Email(message: 'l\'email n\'est pas valide')]
    private ?string $mail = null;

    #[ORM\Column]
    private ?bool $administrateur = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sorties;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, orphanRemoval: true)]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifAnnulation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Vich\UploadableField(mapping: 'participants', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $derniereConnexion = null;

    public function __construct()
    {
        $this->administrateur = false;
        $this->actif = true;
        $this->sorties = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        $this->sorties->removeElement($sorty);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): static
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): static
    {
        $this->imageSize = $imageSize;

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

    public function __serialize()
    {
        return [
            'id' => $this->id,
            "imageName" => $this->imageName,
            'imageSize'=>$this->imageSize,
            'updatedAt'=>$this->updatedAt,
            'pseudo'=>$this->pseudo,
            'role'=>$this->roles,
            'password'=>$this->password,
            'nom'=>$this->nom,
            'prenom'=>$this->prenom,
            'telephone'=>$this->telephone,
            'mail'=>$this->mail,
            'actif'=>$this->actif,
            'site'=>$this->site,
            'sortiesOrganisees'=>$this->sortiesOrganisees,
            'sorties'=>$this->sorties
        ];
    }

    public function __unserialize(array $data)
    {
        $this->id = $data['id'];
        $this->imageName = $data['imageName'];
        $this->imageSize = $data['imageSize'];
        $this->updatedAt = $data['updatedAt'];
        $this->pseudo = $data['pseudo'];
        $this->roles = $data['role'];
        $this->password = $data['password'];
        $this->nom = $data['nom'];
        $this->prenom = $data['prenom'];
        $this->telephone = $data['telephone'];
        $this->mail = $data['mail'];
        $this->actif = $data['actif'];
        $this->site = $data['site'];
        $this->sortiesOrganisees = $data['sortiesOrganisees'];
        $this->sorties = $data['sorties'];
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(?\DateTimeInterface $derniereConnexion): static
    {
        $this->derniereConnexion = $derniereConnexion;

        return $this;
    }
}
