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

namespace SFW2\Core\Handlebars;

use Handlebars\Handlebars;
use Handlebars\Loader\StringLoader;

final class HandlebarsFactory
{

    /**
     * @var Handlebars[]
     */
    private array $handlebars = [];

    /**
     * @param array<string, string> $templateFolders
     * @param string                $defaultNamespace
     */
    public function __construct(
        private readonly array  $templateFolders,
        private readonly string $defaultNamespace = ''
    ) {
    }

    public function getHandlebars(LoaderType $loaderType): Handlebars
    {
        if (isset($this->handlebars[$loaderType->value])) {
            return $this->handlebars[$loaderType->value];
        }

        $loader = match ($loaderType) {
            LoaderType::STRING_LOADER => new StringLoader(),
            LoaderType::TEMPLATE_LOADER => new TemplateLoader($this->templateFolders, $this->defaultNamespace)
        };

        $handlebars = new Handlebars([
            "loader" => $loader,
            "partials_loader" => $loader
        ]);

        /**
         * removes all non-numericale characters, so it can easely used in href="tel:..."
         */
        $handlebars->addHelper(
            "sanitize_phone_nb",
            function ($template, $context, $args, $source) {
                return preg_replace("/[^0-9]/", '', $context->get($args));
            }
        );

        /**
         * makes string a valid css-class-name
         */
        $handlebars->addHelper(
            "identifier",
            function ($template, $context, $args, $source) {
                $tmp = strtolower($context->get($args));
                 // FIXME: I18N issue
                $tmp = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $tmp);
                return preg_replace("/[^0-9a-zA-Z_-]/", '_', $tmp);
            }
        );

        /**
         * pads string with given character (e.g. {{#fillup ItemSubNumber 2}} with ItemSubNumber = 1 leads to '01')
         */
        $handlebars->addHelper(
            "fillup",
            function ($template, $context, $args, $source) {
                preg_match("/(.*?)\s+([0-9]*)/", trim($args), $m);
                $keyname = $m[1];
                $length = (int)$m[2];
                return str_pad((string)$context->get($keyname), $length, '0', STR_PAD_LEFT);
            }
        );

        /**
         * wraps a variable with a given string (e.g. {{#wrap date 'Today is %s'}} if date is empty, empty string is returned)
         */
        $handlebars->addHelper(
            'wrap',
            function ($template, $context, $args, $source) {
                preg_match("/(.*?)\s+[\"'](.*?)[\"']/", trim($args), $m);
                $keyname = $m[1];
                $template = $m[2];
                $value = $context->get($keyname);
                return $value ? printf($template, $value) : '';
            }
        );

        return $this->handlebars[$loaderType->value] = $handlebars;
    }
}