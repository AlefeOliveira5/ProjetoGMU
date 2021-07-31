<?php
/** 
 * @author Alefe
 *
*/
class AcompanhanteForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Acompanhate');
        $this->form->setFormTitle('Acompanhante');

        $id = new THidden('id');
        $nome_fami = new TEntry('nome_familiar');
        $pacie_id = new TDBCombo('pacie_id','DB_GMU','Paciente','id','nome');
        $cel = new TEntry('celular');
        $cpf = new TEntry('cpf');
        $idade = new TEntry('idade');
        $email = new TEntry('email');
        $dtNasc = new TDate('dt_nasc');
        
        
        //Validador
        $nome_fami->addValidation("Nome Acompanhate" , new TRequiredValidator );
        $cel->addValidation("Celular" , new TRequiredValidator );
        $idade->addValidation("Idade" , new TRequiredValidator );
        $dtNasc->addValidation("Data de Nascimento" , new TRequiredValidator );
        $cpf->addValidation("CPF" , new TRequiredValidator );
        $email->addValidation("Email" , new TRequiredValidator );
        $pacie_id->addValidation("Selecione o Paciente: " , new TRequiredValidator );
        
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Nome Acompanhante <font color="red">*</font>')], [$nome_fami]); 
        $this->form->addFields([new TLabel('Celular <font color="red">*</font>')], [$cel]);
        $this->form->addFields([new TLabel('Idade <font color="red">*</font>')], [$idade]); 
        $this->form->addFields([new TLabel('Data Nascimento <font color="red">*</font>')], [$dtNasc]); 
        $this->form->addFields([new TLabel('CPF <font color="red">*</font>')], [$cpf]);
        $this->form->addFields([new TLabel('Email <font color="red">*</font>')], [$email]);
        $this->form->addFields([new TLabel('Selecione o paciente <font color="red">*</font>')], [$pacie_id]);
        
        
     
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);

        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('AcompanhanteList', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        parent::add($this->form);
    }

    public function onEdit($param){
        try {

            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('DB_GMU');

                $obj = new Acompanhante($key);
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

            $object = $this->form->getData('Acompanhante');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('AcompanhanteList', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

?>