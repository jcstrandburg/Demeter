<?php
namespace Jcstrandburg\Demeter;

interface Set extends Collection
{
    /**
     * Returns a copy of this set guaranteed to contain the given item (or one with an identical hash)
     * @param   mixed   $item
     */
    public function add($item): Set;

    /**
     * Returns a copy of this set guaranteed to contain the given items (or items with a identical hashes)
     * @param   iterable    $items
     */
    public function addMany(iterable $items): Set;

    /**
     * Returns a copy of this set guaranteed not to contain the given item (or any with an identical hash)
     * @param   mixed   $item
     */
    public function remove($item): Set;

    /**
     * Returns a copy of this set guaranteed not to contain the given items (or any with identical hashes)
     * @param   iterable    $items
     */
    public function removeMany(iterable $items): Set;

    /**
     * Returns true if this set contains the given item (or one with an identical hash)
     * @param   mixed   $item
     */
    public function contains($item): bool;
}
