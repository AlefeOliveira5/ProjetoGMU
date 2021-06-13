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
class Prescricao extends \Adianti\Database\TRecord{
    const TABLENAME = 'prescricao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    

    public function get_nome_pacie (){ 

        if ( empty ($this->paciente ) ) {
            $this->paciente = new Paciente( $this->idpacie);
        }

        return $this->paciente->nome_pacie;
    }

    public function get_nome_medico (){ 

        if ( empty ($this->medico ) ) {
            $this->medico = new Medico( $this->medico_id);
        }

        return $this->medico->nome_medico;
    }

    public function get_priori (){ 

        if ( empty ($this->lista_espera_uti ) ) {
            $this->lista_espera_uti = new Lista_Espera_UTI( $this->priori_id);
        }

        return $this->lista_espera_uti->priori;
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
