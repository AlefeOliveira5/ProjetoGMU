<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tutor
 *
 * @author Alefe
 */
class LeitosUTI extends \Adianti\Database\TRecord{
    const TABLENAME = 'leito_uti';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    public function get_pacienteID (){ 

        if ( empty ($this->lista_espera_uti ) ) {
            $this->lista_espera_uti = new Lista_Espera_UTI( $this->nome);
        }

        return $this->lista_espera_uti->pacienteID;
    }
    
   /*public function get_nome_pacie (){ 

        if ( empty ($this->paciente ) ) {
            $this->paciente = new Paciente( $this->idPacie);
        }

        return $this->paciente->nome_pacie;
    }
    
    /**
     * Constructor method
     */

}
