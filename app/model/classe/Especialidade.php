<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of medico
 *
 * @author Alefe
 */
class Especialidade extends \Adianti\Database\TRecord{
    const TABLENAME = 'especialidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    /**
     * Constructor method
     */
    /*public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_medico');
        parent::addAttribute('CRMV');
        parent::addAttribute('cpf');
    }*/
}
