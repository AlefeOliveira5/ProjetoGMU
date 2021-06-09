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
class Medico extends \Adianti\Database\TRecord{
    const TABLENAME = 'medico';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    

    public function get_nome_espec (){ 

        if ( empty ($this->especialidade ) ) {
            $this->especialidade = new Especialidade( $this->espec_id);
        }

        return $this->especialidade->nome_espec;
    }
    
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
