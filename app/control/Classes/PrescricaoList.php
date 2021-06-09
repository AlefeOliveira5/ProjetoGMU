<?php
/**
 * @author Alefe
 */
class PrescricaoList extends TPage
{
    protected $form;
    protected $datagrid;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('list_prescricao');
        $this->form->setFormTitle('Prescrição');

        // 
        $opcao = new TCombo('opcao');
        //$nome = new TEntry('a'); 
        $medico = new TEntry('medico_id');
        $data_consulta = new TDate('data_consulta');

        $items= array();
        $items['medico_id'] = 'Médico';
        $items['data_consulta'] = 'Data';

        $opcao->addItems($items);
        $opcao->setValue('medico_id');
        $opcao->setValue('data_consulta');

        $opcao->setDefaultOption(FALSE);

        //$nome->setSize('80%');
        $opcao->setSize('80%');

        $this->form->addFields( [new TLabel('Selecione o campo')], [$opcao]);
        $this->form->addFields( [new TLabel('Buscar')], [$data_consulta]);

        $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addAction('Novo',  new TAction(array('PrescricaoForm', 'onEdit')), 'fa:plus green');
        $this->form->addAction( 'Limpar Busca' , new TAction(array($this, 'onClear')), 'fa:eraser red');