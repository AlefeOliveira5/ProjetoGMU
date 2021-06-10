<?php
/**
 * @author Alefe
 */
class PrescricaoForm extends TPage{

    private $form;

    public function __construct(){
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_Consulta');
        $this->form->setFormTitle('Consulta');

        $id = new THidden('id');
        $medico= new TDBCombo('medico_id','DB_GMU','Medico','id','nome_medico');
        $paciente= new TDBCombo('idpacie','DB_GMU','Paciente','id','nome_pacie');
        //$exame= new TDBCombo('exame_id','bancomysql','Exames','id','nome_exame');
        $data_consulta = new TDate('data_consulta');
        
        //Diagnostico
        $diagnostico  = new TText('diagnostico');

        $exame->setDefaultOption('Selecione');
        
        //Validador
        $medico->addValidation("Medico" , new TRequiredValidator );
        $paciente->addValidation("Paciente" , new TRequiredValidator );
        $data_consulta->addValidation("Data Consulta" , new TRequiredValidator );

        $data_consulta->setMask('dd/mm/yyyy');

        $this->form->appendPage('Marca Consulta');
        $this->form->addFields([$id]);     
        $this->form->addFields([new TLabel('Médico <font color="red">*</font>')], [$medico]); 
        $this->form->addFields([new TLabel('Paciente <font color="red">*</font>')], [$paciente]);
        $this->form->addFields([new TLabel('Data Consulta <font color="red">*</font>')], [$data_consulta]);
        
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<font color="red">*</font> Campos obrigatórios' ) ]);
        
        $this->form->appendPage('Diagnôsticos');
        //$this->form->addFields([new TLabel('Exame')], [$exame]);
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

                $obj = new Consulta ($key);
                $obj->data_consulta = TDate::date2br($obj->data_consulta);
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
            $object->data_consulta = TDate::date2us($object->data_consulta);
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