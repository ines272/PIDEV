<?php

namespace App\Enum;

enum PetType: string
{
    case CHAT = 'CHAT';
    case CHIEN = 'CHIEN';
    case TORTUE = 'TORTUE';
    case LAPIN = 'LAPIN';
    case OISEAU = 'OISEAU';
    case POISSON = 'POISSON';
    case SOURIS = 'SOURIS';
}
