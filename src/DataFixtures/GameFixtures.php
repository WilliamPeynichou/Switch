<?php

namespace App\DataFixtures;

use App\Entity\Difficulty;
use App\Entity\Game;
use App\Entity\GamePosition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer les difficultés
        $difficulties = [
            ['name' => 'Débutant', 'level' => 1, 'description' => 'Pour les nouveaux joueurs', 'colorCode' => '#4CAF50'],
            ['name' => 'Intermédiaire', 'level' => 2, 'description' => 'Niveau moyen', 'colorCode' => '#FF9800'],
            ['name' => 'Expert', 'level' => 3, 'description' => 'Pour les joueurs expérimentés', 'colorCode' => '#F44336'],
            ['name' => 'Pro', 'level' => 4, 'description' => 'Niveau professionnel', 'colorCode' => '#9C27B0']
        ];

        foreach ($difficulties as $diffData) {
            $difficulty = new Difficulty();
            $difficulty->setName($diffData['name'])
                      ->setLevel($diffData['level'])
                      ->setDescription($diffData['description'])
                      ->setColorCode($diffData['colorCode'])
                      ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($difficulty);
        }

        // Créer les jeux
        $games = [
            [
                'name' => 'Tour du Monde',
                'description' => 'Le classique du basket ! Parcourez toutes les positions du terrain.',
                'baseRules' => 'Commencez à la position 1. Marquez un panier pour passer à la position suivante. Airball = -2 positions, Brique = -1 position.',
                'minPlayers' => 2,
                'maxPlayers' => 8,
                'defaultDurationMinutes' => 30,
                'gameType' => 'position_based',
                'positions' => [
                    ['number' => 1, 'name' => 'Côté gauche', 'description' => '45° à gauche du panier', 'x' => 0.2, 'y' => 0.3, 'points' => 1],
                    ['number' => 2, 'name' => 'Face panier', 'description' => 'Devant le panier', 'x' => 0.5, 'y' => 0.2, 'points' => 1],
                    ['number' => 3, 'name' => 'Côté droit', 'description' => '45° à droite du panier', 'x' => 0.8, 'y' => 0.3, 'points' => 1],
                    ['number' => 4, 'name' => 'Ligne 3 points', 'description' => 'Derrière la ligne des 3 points', 'x' => 0.5, 'y' => 0.1, 'points' => 2],
                    ['number' => 5, 'name' => '1 contre 1', 'description' => 'Duel final contre un adversaire', 'x' => 0.5, 'y' => 0.5, 'points' => 3, 'final' => true]
                ]
            ],
            [
                'name' => 'HORSE',
                'description' => 'Épellez H-O-R-S-E en marquant des paniers. Ratez et vous gagnez une lettre !',
                'baseRules' => 'Le premier joueur choisit un tir. Les autres doivent reproduire le même tir. Si vous ratez, vous gagnez une lettre.',
                'minPlayers' => 2,
                'maxPlayers' => 6,
                'defaultDurationMinutes' => 20,
                'gameType' => 'position_based',
                'positions' => [
                    ['number' => 1, 'name' => 'H', 'description' => 'Première lettre', 'x' => 0.3, 'y' => 0.4, 'points' => 1],
                    ['number' => 2, 'name' => 'O', 'description' => 'Deuxième lettre', 'x' => 0.5, 'y' => 0.4, 'points' => 1],
                    ['number' => 3, 'name' => 'R', 'description' => 'Troisième lettre', 'x' => 0.7, 'y' => 0.4, 'points' => 1],
                    ['number' => 4, 'name' => 'S', 'description' => 'Quatrième lettre', 'x' => 0.3, 'y' => 0.6, 'points' => 1],
                    ['number' => 5, 'name' => 'E', 'description' => 'Cinquième lettre - Élimination !', 'x' => 0.5, 'y' => 0.6, 'points' => 1, 'final' => true]
                ]
            ],
            [
                'name' => 'Shoot-out',
                'description' => 'Tir chronométré ! Marquez le plus de paniers possible en un temps limité.',
                'baseRules' => 'Chaque position a un temps limite. Marquez le plus de paniers possible !',
                'minPlayers' => 1,
                'maxPlayers' => 10,
                'defaultDurationMinutes' => 15,
                'gameType' => 'time_based',
                'positions' => [
                    ['number' => 1, 'name' => 'Position 1', 'description' => 'Tir rapide', 'x' => 0.2, 'y' => 0.3, 'points' => 1],
                    ['number' => 2, 'name' => 'Position 2', 'description' => 'Tir rapide', 'x' => 0.5, 'y' => 0.2, 'points' => 1],
                    ['number' => 3, 'name' => 'Position 3', 'description' => 'Tir rapide', 'x' => 0.8, 'y' => 0.3, 'points' => 1],
                    ['number' => 4, 'name' => 'Position 4', 'description' => 'Tir rapide', 'x' => 0.3, 'y' => 0.6, 'points' => 1],
                    ['number' => 5, 'name' => 'Position 5', 'description' => 'Tir rapide', 'x' => 0.7, 'y' => 0.6, 'points' => 1]
                ]
            ],
            [
                'name' => 'King of the Court',
                'description' => 'Le roi du terrain ! Gagnez pour rester, perdez pour sortir.',
                'baseRules' => '1 contre 1 en continu. Le gagnant reste, le perdant sort. Le dernier debout gagne !',
                'minPlayers' => 3,
                'maxPlayers' => 8,
                'defaultDurationMinutes' => 25,
                'gameType' => 'score_based',
                'positions' => [
                    ['number' => 1, 'name' => 'Terrain', 'description' => '1 contre 1', 'x' => 0.5, 'y' => 0.5, 'points' => 1, 'final' => true]
                ]
            ]
        ];

        foreach ($games as $gameData) {
            $game = new Game();
            $game->setName($gameData['name'])
                 ->setDescription($gameData['description'])
                 ->setBaseRules($gameData['baseRules'])
                 ->setMinPlayers($gameData['minPlayers'])
                 ->setMaxPlayers($gameData['maxPlayers'])
                 ->setDefaultDurationMinutes($gameData['defaultDurationMinutes'])
                 ->setGameType($gameData['gameType'])
                 ->setOfficial(true)
                 ->setImageUrl('/images/games/' . strtolower(str_replace(' ', '-', $gameData['name'])) . '.jpg')
                 ->setCreatedAt(new \DateTimeImmutable())
                 ->setUpdatedAt(new \DateTimeImmutable())
                 ->setActive(true);
            
            $manager->persist($game);

            // Ajouter les positions pour chaque jeu
            foreach ($gameData['positions'] as $posData) {
                $position = new GamePosition();
                $position->setGame($game)
                         ->setPositionNumber($posData['number'])
                         ->setPositionName($posData['name'])
                         ->setPositionDescription($posData['description'])
                         ->setCoordinatesX($posData['x'])
                         ->setCoordinatesY($posData['y'])
                         ->setPointsValue($posData['points'])
                         ->setFinalPosition($posData['final'] ?? false)
                         ->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($position);
            }
        }

        $manager->flush();
    }
}