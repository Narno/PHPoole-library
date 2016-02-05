<?php
/*
 * Copyright (c) Arnaud Ligny <arnaud@ligny.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPoole\Taxonomy;

use PHPoole\Collection\AbstractCollection;
use PHPoole\Collection\ItemInterface;

/**
 * Class Vocabulary.
 */
class Vocabulary extends AbstractCollection implements ItemInterface
{
    /**
     * Adds term to a Vocabulary collection.
     * {@inheritdoc}
     */
    public function add(ItemInterface $item)
    {
        if ($this->has($item->getId())) {
            // return if already exists
            return $this;
        }
        $this->items[$item->getId()] = $item;

        return $this;
    }
}
