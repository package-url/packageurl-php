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

namespace PackageUrl;

use Closure;

/**
 * @internal
 *
 * @author jkowaleck
 */
trait BuildParseTrait
{
    /**
     * @psalm-assert-if-true non-empty-string $data
     */
    private function isNotEmpty(string $data): bool
    {
        return '' !== $data;
    }

    /**
     * @psalm-assert-if-true non-empty-string $segment
     */
    private function isUsefulSubpathSegment(string $segment): bool
    {
        return false === \in_array($segment, ['', '.', '..'], true);
    }

    /**
     * @psalm-return Closure(non-empty-string):non-empty-string
     */
    private function getNormalizerForNamespace(?string $type): \Closure
    {
        if (null !== $type) {
            $type = strtolower($type);
        }
        if (\in_array($type, ['bitbucket', 'deb', 'github', 'golang', 'hex', 'rpm', 'composer'], true)) {
            return static function (string $data): string {
                return strtolower($data);
            };
        }

        return static function (string $data): string {
            return $data;
        };
    }

    /**
     * Normalize MLflow package name based on qualifiers.
     *
     * MLflow purl names are case-sensitive for Azure ML (keep as-is)
     * and case-insensitive for Databricks (lowercase).
     *
     * @param mixed $qualifiers Can be string, array, or null
     */
    public function normalize_mlflow_name(string $name, $qualifiers): ?string
    {
        if (\is_array($qualifiers)) {
            $repoUrl = $qualifiers['repository_url'] ?? null;

            if (null !== $repoUrl) {
                $repoUrlLower = strtolower($repoUrl);
                if (str_contains($repoUrlLower, 'azureml')) {
                    return $name;
                }
                if (str_contains($repoUrlLower, 'databricks')) {
                    return strtolower($name);
                }
            }
        } elseif (\is_string($qualifiers)) {
            $qualifiersLower = strtolower($qualifiers);
            if (str_contains($qualifiersLower, 'azureml')) {
                return $name;
            }
            if (str_contains($qualifiersLower, 'databricks')) {
                return strtolower($name);
            }
        }

        return $name;
    }

    /**
     * @psalm-param  non-empty-string $name
     *
     * @return non-empty-string
     */
    private function normalizeNameForType(string $name, ?string $type, $qualifiers): string
    {
        if (null !== $type) {
            $type = strtolower($type);

            if (!preg_match('/^[a-z0-9._-]+$/i', $type)) {
                throw new \InvalidArgumentException(\sprintf('Type must be composed only of ASCII letters, numbers, period, dash, or underscore: "%s"', $type));
            }

            if (isset($type[0]) && ctype_digit($type[0])) {
                throw new \InvalidArgumentException(\sprintf('Type cannot start with a number: "%s"', $type));
            }
        }
        if ('pypi' === $type) {
            /**
             * note for psalm that the length did not change.
             *
             * @psalm-var non-empty-string $name
             */
            $name = str_replace('_', '-', $name);
        } elseif ('mlflow' === $type) {
            $name = $this->normalize_mlflow_name($name, $qualifiers);
        }

        if (\in_array($type, ['bitbucket', 'deb', 'github', 'golang', 'hex', 'npm', 'pypi', 'composer'], true)) {
            $name = strtolower($name);
        }

        return $name;
    }
}
