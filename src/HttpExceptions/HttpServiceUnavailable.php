<?php

/**
 *  SFW2 - SimpleFrameWork
 *
 *  Copyright (C) 2017  Stefan Paproth
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
 *
 */

declare(strict_types=1);

namespace SFW2\Core\HttpExceptions;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class HttpServiceUnavailable extends HttpException
{
    public function __construct(string $msg = 'Service Unavailable', Throwable $prev = null)
    {
        parent::__construct(
            'Die Seiten sind aktuell offline',
            'Aufgrund von umfangreichen Wartungsarbeiten sind die Webseiten im Moment leider nicht '.
            'zu erreichen. Bitte versuche es später noch einmal.',
            $msg,
            StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE,
            $prev
        );
    }
}