<?php

/**
 * This file is part of the contentful/contentful-management package.
 *
 * @copyright 2015-2020 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Management\Resource\ContentType\Field;

/**
 * LocationField class.
 */
class LocationField extends BaseField
{
    public function getType(): string
    {
        return 'Location';
    }
}