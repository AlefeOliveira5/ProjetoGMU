<?php
/**
 * @author Alefe
 */
class PrescricaoForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Prescricao');
        $this->form->setFormTitle('Prescrição');

        $id = new THidden('id');
        $idpacie = new THidden('idpacie');
        $medico_id = new THidden('medico_id');
        $nome_medico = new TDBSeekButton('nome_medico', 'DB_GMU', $this->form->getName(),'Medico', 'nome', 'medico_id', 'nome_medico');
        $nome_medico->setProperty('placeholder', 'Clique na Lupa.');
        $nome_medico->setSize("200");
        $nome_pacie = new TDBSeekButton('nome_pacie', 'DB_GMU', $this->form->getName(),'Paciente', 'nome', 'idpacie', 'nome_pacie');
        $nome_pacie->setProperty('placeholder', 'Clique na Lupa.');
        $nome_pacie->setSize("200");
        $data_consulta = new TDate('dtconsulta');
        
        //Diagnostico
        $diagnostico  = new TText('diagnostico');
        
        //Validador
        $data_consulta->addValidation("Data Prescrição" , new TRequiredValidator );

        $data_consulta->setMask('dd/mm/yyyy');

        $this->form->appendPage('Prescrição');
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Médico <font color="red">*</font>')], [$nome_medico],[$medico_id]); 
        $this->form->addFields([new TLabel('Paciente <font color="red">*</font>')], [$nome_pacie],[$idpacie]);
        $this->form->addFields([new TLabel('Data Prescrição <font color="red">*</font>')], [$data_consulta]);
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
        
        $this->form->appendPage('Diagnôsticos');
        $this->form->addFields([new TLabel('Diagnôstico <font color="red">*</font>')], [$diagnostico]);

        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
    

        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('PrescricaoList', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        parent::add($this->form);
    }

    public function onEdit($param){
        try {

            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('DB_GMU');

                $obj = new Prescricao ($key);
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

            $object = $this->form->getData('Prescricao');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('PrescricaoList', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}

?>