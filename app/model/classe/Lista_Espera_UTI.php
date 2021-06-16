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
class Lista_Espera_UTI extends \Adianti\Database\TRecord{
    const TABLENAME = 'lista_espera_uti';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    public function get_nome_familiar (){ 

        if ( empty ($this->familiar ) ) {
            $this->familiar = new Acompanhante( $this->acompID);
        }

        return $this->familiar->nome_familiar;
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
