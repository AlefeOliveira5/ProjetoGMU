<?php

class ListaEsperaUTIForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_lista_espera_UTI');
        $this->form->setFormTitle('Cadastro Lista Espera UTI');

        $id = new THidden('id');
        $pacID= new TDBCombo('pacienteID','DB_GMU','Paciente','id','nome_pacie');
        $acomp= new TDBCombo('acompID','DB_GMU','Acompanhante','id','nome_familiar');
        $priori = new TEntry('priori');
        $telA = new TEntry('telAcomp');
        
        

        
        //Validador
        $pacID->addValidation("Nome Paciente" , new TRequiredValidator );
        $acomp->addValidation("Acompanhante" , new TRequiredValidator );
        $priori->addValidation("Prioridade" , new TRequiredValidator );
        $telA->addValidation("Celular Acompanhante" , new TRequiredValidator );
        

        
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Nome Paciente <font color="red">*</font>')], [$pacID]);
        $this->form->addFields([new TLabel('Acompanhante <font color="red">*</font>')], [$acomp]);
        $this->form->addFields([new TLabel('Tipo de Prioridade <font color="red">*</font>')], [$priori]);
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font><i>ATENÇÃO: Deve Ser Definido o tipo de Prioridade Como:
        <br> P1, P2, P3, P4 e P5</i>' ) ]);

        $this->form->addFields([new TLabel('Celular Acompanhante <font color="red">*</font>')], [$telA]);
        
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
        
    
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('ListaEsperaUTIList', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        parent::add($this->form);
    }

    public function onEdit($param){
        try {

            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('DB_GMU');

                $obj = new Paciente($key);
                $this->form->setData($obj);

                TTransaction::close();
            }

        } catch (Exception $e) {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage() . "<br/>");

            TTransaction::rollback();
        }
    }

    public function onSave($param){
        try {
            TTransaction::open('DB_GMU');

            $this->form->validate();

            $object = $this->form->getData('Lista_Espera_UTI');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('ListaEsperaUTIList', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

?>