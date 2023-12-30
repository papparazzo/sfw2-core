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
 *
 */

declare(strict_types=1);

namespace SFW2\Core\Utils;

use Exception;
use Handlebars\Handlebars;
use Handlebars\Loader;

class Mailer
{
    protected Handlebars $handlebars;

    public function __construct(
        Loader $loader,
        private readonly string $from,
        private readonly array $bcc = []
    ) {
          $this->handlebars = new Handlebars([
            "loader" => $loader,
            "partials_loader" => $loader
        ]);
    }

    /**
     * @throws Exception
     */
    public function sendTemplate(string $addr, string $subject, string $template, array $data = []): void
    {
        $text = $this->handlebars->render($template, $data);
        $this->send($addr, $subject, $text, $data);
    }

    /**
     * @throws Exception
     */
    public function send(string $addr, string $subject, string $text, array $data = []): void
    {
        $headers = [
            'MIME-Version'              => '1.0',
            'Content-type'              => 'text/html; charset=utf-8',
            'Content-Transfer-Encoding' => '8bit',
            'From'                      => $this->from,
            'Bcc'                       => implode(',', $this->bcc)
        ];

        if (!mail($addr, $subject, $text, $headers)) {
            throw new Exception("Could not send body <$text> to <$addr>");
        }
    }
}