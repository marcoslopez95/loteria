<?php

namespace App\Enums;

/**
 * Order lifecycle status.
 *
 * Values are stored as strings in the database.
 */
enum OrderStatus: string
{
    case PorPagar = 'PorPagar';
    case Pagada = 'Pagada';
    case Cancelada = 'Cancelada';
}
