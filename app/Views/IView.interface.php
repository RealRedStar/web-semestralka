<?php

namespace redstar\Views;

/**
 * Rozhraní pro všechny šablony
 * @package redstar\Views
 */
interface IView
{
    /**
     * Zajistí vypsání HTML šablony příslušné stránky
     * @param array $tplData
     */
    public function printOutput(array $tplData);
}