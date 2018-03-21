<?php
namespace Solean\CleanProspecter;

interface Presenter
{
    public function present($response) : array;
}