<?php
declare(strict_types=1);

class Card extends Index
{
    public function getRenderVars(): array
    {
        $cards = $this->getCards($this->getRequestVars());

        echo base64_decode($cards[0]['image']);
        exit();
    }

}