<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SessionUpdateDTO
{
    #[Assert\NotBlank(message: 'L\'ID du participant est requis')]
    #[Assert\Type(type: 'integer', message: 'L\'ID du participant doit être un entier')]
    public ?int $participantId = null;

    #[Assert\NotBlank(message: 'La position est requise')]
    #[Assert\Type(type: 'integer', message: 'La position doit être un entier')]
    #[Assert\Range(min: 1, max: 10, notInRangeMessage: 'La position doit être entre 1 et 10')]
    public ?int $position = null;

    #[Assert\Choice(choices: ['position_change', 'shot_made', 'shot_missed', 'airball', 'brick'], message: 'Type d\'événement invalide')]
    public string $eventType = 'position_change';

    public function __construct(array $data = [])
    {
        $this->participantId = $data['participantId'] ?? null;
        $this->position = $data['position'] ?? null;
        $this->eventType = $data['eventType'] ?? 'position_change';
    }
}
