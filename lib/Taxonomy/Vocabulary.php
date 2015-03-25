<?php
/*
 * Copyright (c) Arnaud Ligny <arnaud@ligny.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPoole\Taxonomy;

use PHPoole\Collection\Collection as AbstractCollecton;
use PHPoole\Collection\ItemInterface;

/**
 * Class Vocabulary
 * @package PHPoole\Taxonomy
 */
class Vocabulary extends AbstractCollecton implements ItemInterface
{
    protected $name;

    /**
     * Create vocabulary
     *
     * @param array $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Return vocabulary name
     *
     * @return array
     */
    public function getId()
    {
        return $this->name;
    }

    /**
     * Adds term to vocabulary
     *
     * @param ItemInterface $item
     * @return $this
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