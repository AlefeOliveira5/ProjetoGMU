<?php
/**
 * @author Alefe
 */
class AcompanhanteList extends TPage
{
    protected $form;
    protected $datagrid;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('list_acompanhante');
        $this->form->setFormTitle('Acompanhante');

        // 
        $opcao = new TCombo('opcao');
        $nome = new TEntry('nome_familiar'); //variavel q vc vai buscar

        $items= array();
        $items['nome_familiar'] = 'Nome Acompanhante';
        $items['cpf'] = 'CPF';
        $items['celular'] = 'Celular';

        $opcao->addItems($items);
        $opcao->setValue('nome_familiar');
        $opcao->setValue('celular');
        $opcao->setValue('cpf');
       
        
        $opcao->setDefaultOption(FALSE);

        $nome->setSize('80%');
        $opcao->setSize('80%');

        $this->form->addFields( [new TLabel('Selecione o campo')], [$opcao]);
        $this->form->addFields( [new TLabel('Buscar')], [$nome]);

        $btn = $this->form->addAction('Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addAction('Novo',  new TAction(array('AcompanhanteForm', 'onEdit')), 'fa:plus green');
        $this->form->addAction( 'Limpar Busca' , new TAction(array($this, 'onClear')), 'fa:eraser red');

        //DATAGRID ------------------------------------------------------------------------------------------

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // DATA GRID
        $nome = new TDataGridColumn('nome_familiar', 'Nome Acompanhante', 'left');
        $pacie = new TDataGridColumn('nome_pacie', 'Paciente', 'left');
        $celular = new TDataGridColumn('celular', 'Celular', 'left');
        $cpf2 = new TDataGridColumn('cpf', 'CPF', 'left');

        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($pacie);
        $this->datagrid->addColumn($celular);
        $this->datagrid->addColumn($cpf2);
      
        // fim data grid

        $actionEdit = new TDataGridAction(array('AcompanhanteForm', 'onEdit'));
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

            $object = new Acompanhante ($param['key']); // SEU RECORD <

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

            $repository = new TRepository('Acompanhante');

            $criteria = new TCriteria;
            $criteria->setProperty('order', 'nome_familiar');

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

            if( !empty( $data->opcao ) && !empty( $data->nome_familiar ) ) {

                $filter = [];

                switch ( $data->opcao ) {

                    default:
                        $filter[] = new TFilter( "LOWER(" . $data->opcao . ")", "LIKE", "NOESC:LOWER( '%" . $data->nome_familiar . "%' )" );
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