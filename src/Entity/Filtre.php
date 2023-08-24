<?php

namespace App\Entity;

use App\Repository\FiltreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


class Filtre
{

    private ?int $id = null;


    private Site|null $site = null;


    private ?string $nom = null;


    private ?\DateTimeInterface $dateHeureDebut = null;


    private ?\DateTimeInterface $dateHeureFin = null;


    private ?bool $organisateur = false;


    private ?bool $inscrit = null;


    private ?bool $datePassee = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Site|null
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * @param Site|null $site
     */
    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }



    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(?\DateTimeInterface $dateHeureFin): static
    {
        $this->dateHeureFin = $dateHeureFin;

        return $this;
    }

    public function isOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function isInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): static
    {
        $this->inscrit = $inscrit;

        return $this;
    }

    public function isDatePassee(): ?bool
    {
        return $this->datePassee;
    }

    public function setDatePassee(?bool $datePassee): static
    {
        $this->datePassee = $datePassee;

        return $this;
    }
}
