<?php
namespace Solean\CleanProspector;

interface Presenter
{
    public function present($response) : array;
}