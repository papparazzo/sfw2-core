<?php

/**
 *  SFW2 - SimpleFrameWork
 *
 *  Copyright (C) 2023  Stefan Paproth
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/agpl.txt>.
 */

declare(strict_types=1);

namespace SFW2\Core\Permission;

use Throwable;
use ValueError;

enum AccessType
{
    case FORBIDDEN;

    case RESTRICTED; // only own recordsets

    case FULL;       // all recordsets

    public static function getByName(string $name): self
    {
        try {
            return self::{$name};
            /** @phpstan-ignore catch.neverThrown */
        } catch (Throwable $th) {
            throw new ValueError("<$name> is not a valid access-type");
        }
    }
}

