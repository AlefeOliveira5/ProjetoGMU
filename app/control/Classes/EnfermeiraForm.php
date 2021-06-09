<?php

class EnfermeiraForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Enfermeira');
        $this->form->setFormTitle('Enfermeira');

        $id = new THidden('id');
        $nome_enfer = new TEntry('nome_enfer');
        $coren = new TEntry('coren');
        $cpf = new TEntry('cpf');
        $email = new TEntry('email');
        $cel = new TEntry('celular');

        
        //Validador
        $nome_enfer->addValidation("Nome Enfermeira" , new TRequiredValidator );
        $coren->addValidation("COREN" , new TRequiredValidator );
        $cpf->addValidation("CPF" , new TRequiredValidator );
        $email->addValidation("Email" , new TRequiredValidator );
        $cel->addValidation("Celular" , new TRequiredValidator );

        
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Nome Enfermeira <font color="red">*</font>')], [$nome_enfer]);
        $this->form->addFields([new TLabel('COREN <font color="red">*</font>')], [$coren]);
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> <i>ATENÇÃO: O número da inscrição deve estar conforme o exemplo abaixo:
            <br>Enfermeiro: 999999-ENF ou Técnico de Enfermagem: 999999-TE ou Auxiliar de Enfermagem: 999999-AE</i>' ) ]);
        $this->form->addFields([new TLabel('CPF <font color="red">*</font>')], [$cpf]);
        $this->form->addFields([new TLabel('Email <font color="red">*</font>')], [$email]);
        $this->form->addFields([new TLabel('Celular <font color="red">*</font>')], [$cel]);
     
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
        
    
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('EnfermeiraList', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        parent::add($this->form);
    }

    public function onEdit($param){
        try {

            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('DB_GMU');

                $obj = new Enfermeira($key);
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

            $object = $this->form->getData('Enfermeira');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('EnfermeiraList', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

?>