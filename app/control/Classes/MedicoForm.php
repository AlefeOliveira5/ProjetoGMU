<?php

class MedicoForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Medico');
        $this->form->setFormTitle('Médico');

        $id = new THidden('id');
        $espec= new TDBCombo('espec_id','DB_GMU','Especialidade','id','nome_espec');
        $nome_medico = new TEntry('nome_medico');
        $crm2 = new TEntry('CRM');
        $cpf = new TEntry('cpf');
        $email = new TEntry('email');
        $cel = new TEntry('celular');

        
        //Validador
        $nome_medico->addValidation("Nome Médico" , new TRequiredValidator );
        $espec->addValidation("Especialidade" , new TRequiredValidator );
        $crm2->addValidation("CRM" , new TRequiredValidator );
        $cpf->addValidation("CPF" , new TRequiredValidator );
        $email->addValidation("Email" , new TRequiredValidator );
        $cel->addValidation("Celular" , new TRequiredValidator );

        
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Nome Médico <font color="red">*</font>')], [$nome_medico]);
        $this->form->addFields([new TLabel('Especialidade <font color="red">*</font>')], [$espec]);
        $this->form->addFields([new TLabel('CRM <font color="red">*</font>')], [$crm2]);
        $this->form->addFields([new TLabel('CPF <font color="red">*</font>')], [$cpf]);
        $this->form->addFields([new TLabel('Email <font color="red">*</font>')], [$email]);
        $this->form->addFields([new TLabel('Celular <font color="red">*</font>')], [$cel]);
     
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
    
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('MedicoList', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        parent::add($this->form);
    }

    public function onEdit($param){
        try {

            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('DB_GMU'); //Arquivo de conexão Banco de dados

                $obj = new Medico($key);
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
            TTransaction::open('DB_GMU'); //Arquivo de conexão Banco de dados

            $this->form->validate();

            $object = $this->form->getData('Medico');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('MedicoList', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

?>