<?php

class LeitosUTIView extends TPage
{
    private $form, $cards, $pageNavigation;
    
    use Adianti\Base\AdiantiStandardCollectionTrait;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        //$this->setDatabase('DB_GMU');
        //$this->setActiveRecord('LeitosUTI');
        //$this->addFilterField('opcao');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Product');
        $this->form->setFormTitle(('LEITOS'));
        
        $opcao = new TEntry('opcao');

        $this->form->addFields( [new TLabel('Selecione a Opção:')], [$opcao] );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addActionLink('Novo',  new TAction(['LeitosUTIForm', 'onEdit']), 'fa:plus-circle green');
        // keep the form filled with the search data
        $opcao->setValue( TSession::getValue( 'opcao' ) );

        //$this->datagrid->add($nome);
        
        // creates the Card View
        $this->cards = new TCardView;
        $this->cards->setContentHeight(170);
        $this->cards->setTitleAttribute('{nome}
                                            <label class="switch">
                                            <input type="checkbox">
                                            <span class="slider round"></span>
                                            </label>
                                            <style>C:\laragon\www\template\app\templates\theme4\css\Ligar_desligar.css</style>');
        
        $this->setCollectionObject($this->cards);
        
        $this->cards->setItemTemplate('<div style="float:left;width:50%;padding-right:10px">
                                           <b>Nome</b> <br> {nome} <br>
                                           </div>');

        $edit_action   = new TAction(['LeitosUTIForm', 'onEdit'], ['id'=> '{id}']);
        $delete_action = new TAction([$this, 'onDelete'], ['id'=> '{id}', 'register_state' => 'false']);
        
        $this->cards->addAction($edit_action,   'Edit',   'far:edit bg-blue');
        //$this->cards->addAction($delete_action, 'Delete', 'far:trash-alt bg-red');
    
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        //$this->datagrid->createModel();
        // creates the page navigation
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form); // add a row to the form
        $vbox->add(TPanelGroup::pack('', $this->cards, $this->pageNavigation)); // add a row for page navigation
        
        // add the table inside the page
        parent::add($vbox);
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
            $criteria->setProperty('order', 'nome');

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

            if( !empty( $data->opcao ) && !empty( $data->nome ) ) {

                $filter = [];

                switch ( $data->opcao ) {

                    default:
                        $filter[] = new TFilter( "LOWER(" . $data->opcao . ")", "LIKE", "NOESC:LOWER( '%" . $data->nome. "%' )" );
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