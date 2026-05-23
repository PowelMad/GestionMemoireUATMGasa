<?php

    function libelleNiveau(?string $niveau): string
    {
        return match($niveau) {
            'L3'    => 'Licence',
            'M2'    => 'Master',
            default => ''
        };
    }
?>
