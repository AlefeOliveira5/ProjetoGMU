<?php
/**
 * @author Alefe
 */
class LeitosUTIList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait with onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        //$this->setDefaultOrder('id', 'asc');
       // $this->addFilterField('opcao', 'like');
        
        //$this->setDatabase('DB_GMU');                // defines the database
        //$this->setActiveRecord('LeitosUTI');            // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('list_LeitosUti');
        $this->form->setFormTitle(('Leitos Lista'));
        
        // create the form fields
        $opcao = new TCombo('opcao');
        $nome2 = new TEntry('nome_pacie');
        $tipo_Prio = new TEntry('nome');
        $dtEntry = new TEntry('dtEntrada');

        $items= array();
        $items['nome_pacie'] = 'Paciente';
        $items['nome'] = 'Prioridade';

        $opcao->addItems($items);
        $opcao->setValue('nome_pacie');
        $opcao->setValue('nome');

        $opcao->setDefaultOption(FALSE);
        
        // add a row for the filter field
        $this->form->addFields( [new TLabel('Selecione o campo')], [$opcao]);
        $this->form->addFields( [new TLabel('Buscar')], [$nome2]);
        
        //$this->form->setData( TSession::getValue('ProductList_filter_data') );
        
        $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addAction('Voltar', new TAction(array('LeitosUTIView', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Novo',  new TAction(array('LeitosUTIForm', 'onEdit')), 'fa:plus green');
        $this->form->addAction( 'Limpar Busca' , new TAction(array($this, 'onClear')), 'fa:eraser red');
        
        // expand button
        //$this->form->addExpandButton();
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        // creates the datagrid columns
        $pacie = new TDataGridColumn('nome_pacie', 'Paciente', 'left');
        $priori = new TDataGridColumn('nome', 'Prioridade', 'left');
        $dtE = new TDataGridColumn('dtEntrada', 'Data de Entrada', 'left');

        
     
        $this->datagrid->addColumn($pacie);
        $this->datagrid->addColumn($priori);
        $this->datagrid->addColumn($dtE);

        $actionEdit = new TDataGridAction(array('LeitosUTIForm', 'onEdit'));
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
        
        // create the page container
        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form);
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );

        //$this->datagrid->disableDefaultClick();

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

            $object = new LeitosUTI ($param['key']); // SEU RECORD <

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

            $repository = new TRepository('LeitosUTI');

            $criteria = new TCriteria;
            $criteria->setProperty('order', 'nome_pacie');

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
                        $filter[] = new TFilter( "LOWER(" . $data->opcao . ")", "LIKE", "NOESC:LOWER( '%" . $data->nome_pacie. "%' )" );
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