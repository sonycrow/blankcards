<?php
declare(strict_types=1);

abstract class SectionController {

    private string $name;

    /**
     * SectionController constructor.
     * @param string $name Nombre de la sección
     */
    function __construct(string $name)
    {
        $this->name = strtolower(trim($name));
    }

    /**
     * @return array Devuelve una colección con los valores de esta sección para la vista
     */
    abstract public function getRenderVars(): array;
}