<?php

class LeitosUTIForm extends TPage
{
    protected $form;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Leitos_UTI');
        $this->form->setFormTitle(('Leitos'));
        $this->form->setClientValidation(true);
        
        // create the form fields
        $pacID = new THidden('pacID');
        $pacienteID = new TDBSeekButton('pacienteID', 'DB_GMU', $this->form->getName(),'Lista_Espera_UTI', 'nome_pacie', 'pacID', 'pacienteIDS');
        
        // allow just these extensions
        //$dbunique->addValidation("Selecione o Paciente: " , new TRequiredValidator );
        //$idade->addValidation('Idade', new TRequiredValidator);

        // add the form fields
        $pacienteID->setProperty('placeholder', 'Clique na Lupa.');
        $pacienteID->setSize("200");
        //$this->form->addFields( [new TLabel('Idade', 'red')], [$idade]);
        
        // add the actions
        $this->form->addFields([new TLabel('Paciente')], [$pacienteID],[$pacID]);

        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('LeitosUTIView', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink( 'Lista', new TAction(['LeitosUTIList', 'onReload']), 'fa:table blue');
        parent::add($this->form);

    }

    public function onEdit($param){
        try {

            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('DB_GMU');

                $obj = new LeitosUTI($key);
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

            $object = $this->form->getData('LeitosUTI');
            $object->store();
      
            TTransaction::close();

            $action_voltar = new TAction(array('LeitosUTIView', 'onReload'));

            new TMessage("info", "Registro salvo com sucesso!", $action_voltar);


        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
?>