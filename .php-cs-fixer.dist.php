<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 package-url
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

$header = file_get_contents(__DIR__.'/LICENSE');

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->in(__DIR__.'/examples')
    ->append([__FILE__])
;

return (new PhpCsFixer\Config())
    ->setUsingCache(true)
    ->setRules(
        /* docs: https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/rules/index.rst
         * docs: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst
         * assistance via tool: https://mlocati.github.io/php-cs-fixer-configurator/
         */
        [
            // region migrate
            // lowest supported PHP
            '@autoPHPMigration:risky' => true,
            '@autoPHPMigration' => true,
            // endregion migrate
            // region general code style
            '@Symfony' => true,
            '@Symfony:risky' => true,
            // endregion general code style
            // lowest supported Phpunit
            '@autoPHPUnitMigration:risky' => true,
            // endregion migrate
            // region customs
            'declare_strict_types' => true,
            'header_comment' => ['header' => $header],
            'global_namespace_import' => true,
            'fopen_flags' => ['b_mode' => true],
            'phpdoc_order' => true,
            'phpdoc_to_comment' => [
                'ignored_tags' => [
                    // needed when PSALM introduced some issues that only manual hints can solve
                    'psalm-var',
                    'psalm-suppress',
                ],
            ],
            // endregion customs
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder);
