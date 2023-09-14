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
 *  along with this program. If not, see <https://www.gnu.org/licenses/agpl.txt>.
 *
 */

declare(strict_types=1);

namespace SFW2\Core\HttpExceptions;

use SFW2\Core\SFW2Exception;
use Throwable;

abstract class HttpException extends SFW2Exception
{
    protected string $title;
    
    protected string $caption;

    protected string $description;
    
    public function __construct(string $caption, string $descripton, string $originMsg, int $code, Throwable $prev = null)
    {
        parent::__construct($originMsg, $code, $prev);
        $this->title = (string)$code;
        $this->caption = $caption;
        $this->description = $descripton;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getInvalidDataGiven() {
        $title = '400';
        $caption = 'Ungültige Daten';
        $description = 'Die Anfrage-Nachricht enthielt ungültige Daten. Bitte prüfe die URL auf Fehler und drücke dann den reload-Button in deinem Browser.';

        return $this->handle($title, $caption, $description);
    }
}