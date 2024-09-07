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

namespace SFW2\Core\Mailer;

use Exception;
use Handlebars\Handlebars;

final class SimpleMailer implements MailerInterface
{
    public function __construct(
        private readonly string     $from,
        private readonly array      $bcc,
        private readonly Handlebars $bodyHandlebars,
        private readonly Handlebars $subjectHandlebars,
    ) {
    }

    /**
     * @throws Exception
     */
    public function send(string $addr, string $subject, string $body, array $data = []): void
    {
        $body = $this->bodyHandlebars->render($body, $data);
        $subject = $this->subjectHandlebars->render($subject, $data);

        $headers = [
            'MIME-Version'              => '1.0',
            'Content-type'              => 'text/html; charset=utf-8',
            'Content-Transfer-Encoding' => '8bit',
            'From'                      => $this->from,
            'Bcc'                       => implode(',', $this->bcc)
        ];

        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'Q');

        if (!mail($addr, $subject, $body, $headers)) {
            throw new Exception("Could not send body <$body> to <$addr>");
        }
    }
}
