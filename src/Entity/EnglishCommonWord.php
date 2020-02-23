<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnglishCommonWordRepository")
 */
class EnglishCommonWord
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
    private $word;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $rank;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getWord(): ?string
    {
        return $this->word;
    }

    /**
     * @param string $word
     * @return EnglishCommonWord
     */
    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     * @return EnglishCommonWord
     */
    public function setRank($rank): self
    {
        $this->rank = $rank;

        return $this;
    }
}
