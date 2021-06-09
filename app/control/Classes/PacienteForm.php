<?php

class PacienteForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_paciente');
        $this->form->setFormTitle('Paciente');

        $id = new THidden('id');
        $nome_pacie = new TEntry('nome_pacie');
        $idade = new TEntry('idade');
        $cpf = new TEntry('cpf');
        $dtNasc = new TDate('dt_nasc_pacie');
        $cel = new TEntry('celular');

        
        //Validador
        $nome_pacie->addValidation("Nome Paciente" , new TRequiredValidator );
        $idade->addValidation("Idade" , new TRequiredValidator );
        $cpf->addValidation("CPF" , new TRequiredValidator );
        $dtNasc->addValidation("Data de Nascimento" , new TRequiredValidator );
        $cel->addValidation("Celular" , new TRequiredValidator );

        $dtNasc->setMask('dd/mm/yyyy');

        
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Nome Paciente <font color="red">*</font>')], [$nome_pacie]);
        $this->form->addFields([new TLabel('Idade <font color="red">*</font>')], [$idade]);
        $this->form->addFields([new TLabel('CPF <font color="red">*</font>')], [$cpf]);
        $this->form->addFields([new TLabel('Data de Nascimento <font color="red">*</font>')], [$dtNasc]);
        $this->form->addFields([new TLabel('Celular <font color="red">*</font>')], [$cel]);
     
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
        
    
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('PacienteList', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

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

            $object = $this->form->getData('Paciente');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('PacienteList', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

?>