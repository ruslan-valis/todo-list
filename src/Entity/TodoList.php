<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TodoListRepository::class)
 */
class TodoList
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TodoListItem", mappedBy="list")
     */
    private $items;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ?Collection
     */
    public function getItems(): ?Collection
    {
        return $this->items;
    }

    /**
     * @param Collection $items
     * @return self
     */
    public function setItems(Collection $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'created_at' => $this->_renderCreatedAt(),
            'updated_at' => $this->_renderUpdatedAt(),
            'items' => $this->_renderListItems() // TODO: Option is to remove items array from here and create separate REST group for receiving items from list (strict REST way)
        ];
    }

    private function _renderCreatedAt(): int {
        $createdAt = $this->getCreatedAt();
        return $createdAt ? $createdAt->getTimestamp() : 0;
    }

    private function _renderUpdatedAt(): int {
        $updatedAt = $this->getUpdatedAt();
        return $updatedAt ? $updatedAt->getTimestamp() : 0;
    }

    // TODO: Define ListItemsCollection instead?
    private function _renderListItems(): array
    {
        $result = [];

        $listItems = $this->getItems();
        if (!$listItems) {
            return $result;
        }

        /** @var TodoListItem $listItem */
        foreach ($listItems as $listItem) {
            $result[] = $listItem->toArray();
        }

        return $result;
    }

}
