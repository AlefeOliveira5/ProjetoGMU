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

        $this->form = new BootstrapFormBuilder('list_consulta');
        $this->form->setFormTitle('Prescrição');

        // 
        $opcao = new TCombo('opcao');
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

        //DATAGRID ------------------------------------------------------------------------------------------

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // DATA GRID
        $nome = new TDataGridColumn('nome_pacie', 'Nome Paciente', 'left');
        $medico = new TDataGridColumn('nome_medico', 'Médico', 'left');
        $data_consulta = new TDataGridColumn('data_consulta', 'Data', 'left');

        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($medico);
        $this->datagrid->addColumn($data_consulta);
      
        // fim data grid

        $actionEdit = new TDataGridAction(array('PrescricaoForm', 'onEdit'));
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage( "far:edit blue" );
        $actionEdit->setField('id');
        $this->datagrid->addAction($actionEdit);

        $actionDelete = new TDataGridAction(array($this, 'onDelete'));
        $actionDelete->setLabel('Deletar');
        $actionDelete->setImage( "far:trash-alt red" );
        $actionDelete->setField('id');
        $this->datagrid->addAction($actionDelete);

        $this->datagrid->createModel();

        //FIM DATAGRID -----------------------------------------------------------------------------------------

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form);
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );

        $this->datagrid->disableDefaultClick();

        parent::add( $container );
    }

    public function onClear() {

        if (TSession::getValue('filter_')) {
            TSession::setValue('filter_', null);
        }

        $this->onReload();

    }

    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) ) {

            $action_ok = new TAction( [ $this, "Delete" ] );
            $action_cancel = new TAction( [ $this, "onReload" ] );

            $action_ok->setParameter( "key", $param[ "key" ] );

            new TQuestion( "Deseja remover o registro?", $action_ok, $action_cancel,  "Deletar");

        }
    }

    function Delete( $param = NULL )
    {
        try {

            TTransaction::open('DB_GMU');

            $object = new Consulta ($param['key']);

            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage( "info", "Registro deletado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error",  $ex->getMessage() .'.' );

        }

    }

    public function onReload( $param = NULL )
    {

        try {

            TTransaction::open('DB_GMU');

            $repository = new TRepository('Prescricao');

            $criteria = new TCriteria;
            $criteria->setProperty('order', 'idpacie');

            if (TSession::getValue('filter_')) {
                $filters = TSession::getValue('filter_');
                foreach ($filters as $filter) {
                    $criteria->add($filter);
                }
            }

            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();

            if ( !empty( $objects ) ) {
                foreach ( $objects as $object ) { 
                    //$object->data_consulta = TDate::date2br($object->data_consulta);            
                    $this->datagrid->addItem( $object );
                }
            }

            $criteria->resetProperties();

            TTransaction::close();

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error",  $ex->getMessage()  );

        }

    }

    public function onSearch()
    {

        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->nome_pacie ) ) {

                $filter = [];

                switch ( $data->opcao ) {

                    default:
                        $filter[] = new TFilter( "LOWER(" . $data->opcao . ")", "LIKE", "NOESC:LOWER( '%" . $data->nome_pacie . "%' )" );
                        break;

                }

                TSession::setValue('filter_', $filter);

                $this->form->setData( $data );

                $this->onReload();

            } else {

                TSession::setValue('filter_', '');

                $this->onReload();

                $this->form->setData( $data );

                new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $data );

            new TMessage( "error",  $ex->getMessage() .'.' );

        }

    }

    public function show()
    {

        $this->onReload();

        parent::show();

    }


}
?>