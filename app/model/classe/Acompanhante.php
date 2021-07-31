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
class Acompanhante extends \Adianti\Database\TRecord{
    const TABLENAME = 'familiar';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    public function get_nome (){ 

        if ( empty ($this->paciente ) ) {
            $this->paciente = new Paciente( $this->pacie_id);
        }

        return $this->paciente->nome;
    }
    
    /**
     * Constructor method
     */

}
