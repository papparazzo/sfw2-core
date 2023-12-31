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

namespace SFW2\Core\Handlebars;

use Handlebars\Handlebars;
use Handlebars\Loader\StringLoader;

final class HandlebarsFactory
{

    private array $handlebars = [];

    public function __construct(
        private readonly array $templateFolders,
        private readonly string $defaultNamespace = ''
    )
    {
    }

    public function getHandlebars(LoaderType $loaderType): Handlebars
    {
        if (isset($this->handlebars[$loaderType->value])) {
            return $this->handlebars[$loaderType->value];
        }

        $loader = match($loaderType) {
            LoaderType::STRING_LOADER => new StringLoader(),
            LoaderType::TEMPLATE_LOADER => new TemplateLoader($this->templateFolders, $this->defaultNamespace)
        };

        $handlebars = new Handlebars([
            "loader" => $loader,
            "partials_loader" => $loader
        ]);

        $handlebars->addHelper("sanitize_phone_nb",
            function($template, $context, $args, $source){
                return preg_replace("/[^0-9]/", '', $context->get($args));
            }
        );

        return $this->handlebars[$loaderType->value] = $handlebars;
    }
}