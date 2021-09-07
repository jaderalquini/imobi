<?php
/**
 * MovbancariaReport Report
 * @author  <your name here>
 */
class MovbancariaReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_Movbancaria_report');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Movbancaria Report');
        
        // create the form fields
        $saldo = new TEntry('saldo');
        $banco_id = new TSeekButton('banco_id');
        $banco = new TEntry('banco');
        $contacorrente_id = new TEntry('contacorrente_id');
        $contacorrente = new TEntry('contacorrente');
        $dtmov = new TDate('dtmov');
        $output_type = new TRadioGroup('output_type');
        
        $obj = new BancoSelectionList;
        $action = new TAction(array($obj, 'onReload'));
        $banco_id->setAction($action);
        
        // Campos Não Editáveis
        $banco->setEditable(FALSE);
        $contacorrente->setEditable(FALSE);
        
        // Formatação para Valores Monetário
        $saldo->setNumericMask(2, ',', '.');
        
        // Máscaras
        $dtmov->setMask('dd/mm/yyyy');
        
        // Tamanho dos Campos no formulário
        $banco_id->setSize(50);
        $banco->setSize(327);
        $contacorrente_id->setSize(100);
        $contacorrente->setSize(277);
        
        // Adiciona validação aos campos
        $saldo->addValidation('Saldo Anterior', new TRequiredValidator);
        $dtmov->addValidation('Do Dia', new TDateValidator);
        $dtmov->addValidation('Do Dia', new TRequiredValidator);
        $output_type->addValidation('Tipo de Saída', new TRequiredValidator);
        
        // Define actions dos campos
        $contacorrente_id->setExitAction(new TAction(array($this, 'onExitContacorrente')));
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));;
        
        // Adiciona Valor Padrão aos Campos
        $saldo->setValue(number_format(0, 2, ',', '.'));
        $dtmov->setValue(date('d/m/Y'));
        $output_type->setValue('pdf');
        
        // Define Layout dos campos RadioGroup e ComboBox
        $output_type->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField('Saldo Anterior', $saldo);
        $this->form->addQuickFields('Banco', array($banco_id, $banco) );
        $this->form->addQuickFields('Conta Corrente', array($contacorrente_id, $contacorrente) );
        $this->form->addQuickField('Do Dia', $dtmov,  100 );
        $this->form->addQuickField('Tipo de Saída', $output_type,  100);
        
        // add the action button
        $this->form->addQuickAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerate()
    {
        try
        {
            TTransaction::open('permission');
            $empresa = new Empresa(TSession::getValue('empresa'));
            TTransaction::close();
            
            TTransaction::open(TSession::getValue('banco'));
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $repository = new TRepository('Movbancaria');
            $criteria   = new TCriteria;
            $criteria->setProperty('order','seq');
                        
            
            if ($formdata->saldo)
            {
                $formdata->saldo = FuncoesExtras::retiraFormatacao($formdata->saldo);
            }
            if ($formdata->banco_id)
            {
                $criteria->add(new TFilter('banco_id', 'like', "%{$formdata->banco_id}%"));
            }
            if ($formdata->contacorrente_id)
            {
                $criteria->add(new TFilter('contacorrente_id', 'like', "%{$formdata->contacorrente_id}%"));
            }
            if ($formdata->dtmov)
            {
                $formdata->dtmov = TDate::date2us($formdata->dtmov);
                $criteria->add(new TFilter('dtmov', 'like', "%{$formdata->dtmov}%"));
                $formdata->dtmov = TDate::date2br($formdata->dtmov);
            }
           
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $widths = array(105,105,105,105,105);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new RelatorioHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new RelatorioPDF($widths);
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new RelatorioRTF($widths);
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '8', 'B',   '#000000', '#ffffff');
                $tr->addStyle('datap', 'Arial', '8', '',    '#111666', '#ffffff');
                $tr->addStyle('datan', 'Arial', '8', '',    'red', '#ffffff');
                $tr->addStyle('header', 'Arial', '14', '',   '#000000', '#ffffff');
                $tr->addStyle('footer', 'Arial', '8', 'B',  '#000000', '#ffffff');
                
                // add a header row
                $tr->addRow();
                $tr->addCell($empresa->nome, 'center', 'header', 5);
                $tr->addRow();
                $tr->addCell('Relatório de Movimentações Bancárias', 'center', 'header', 5);
                
                // add titles row
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('Data', 'left', 'title');
                $tr->addCell($formdata->dtmov, 'right', 'title');
                $tr->addCell('', 'left', 'title');
                $tr->addCell('Saldo Anterior', 'left', 'title');
                $tr->addCell('R$ ' . number_format($formdata->saldo, 2, ',', '.'), 'right', 'title');
                $tr->addRow();
                $tr->addRow();
                $tr->addCell('Historico', 'left', 'title', 3);
                $tr->addCell('Entrada', 'right', 'title');
                $tr->addCell('Saída', 'right', 'title');
                
                // controls the background filling
                //$colour = FALSE;
                
                $totalentrada = 0;
                $totalsaida = 0;
                $totalgeral = 0;
                
                // data rows
                foreach ($objects as $object)
                {
                    $tr->addRow();
                    if ($object->tipo == 'E')
                    {
                        $tr->addCell($object->historico, 'left', 'datap', 3);
                        $tr->addCell('R$ ' . number_format($object->valor, 2, ',', '.'), 'right', 'datap');
                        $tr->addCell('', 'right', 'datap');
                        $totalentrada = $totalentrada + $object->valor;
                    }
                    else
                    {
                        $tr->addCell($object->historico, 'left', 'datan', 3);
                        $tr->addCell('', 'right', 'datan');
                        $tr->addCell('R$ ' . number_format($object->valor, 2, ',', '.'), 'right', 'datan');
                        $totalsaida = $totalsaida + $object->valor;
                    }
                }
                
                $totalgeral = $formdata->saldo + $totalentrada - $totalsaida;
                
                // footer row
                $tr->addRow();
                $tr->addCell('Total', 'left', 'footer', 3);
                $tr->addCell('R$ ' . number_format($totalentrada, 2, ',', '.'), 'right', 'datap');
                $tr->addCell('R$ ' . number_format($totalsaida, 2, ',', '.'), 'right', 'datan');
                $tr->addRow();
                $tr->addCell('Total Geral', 'left', 'footer', 3);
                if ($totalgeral > 0)
                {
                    $tr->addCell('R$ ' . number_format($totalgeral, 2, ',', '.'), 'right', 'datap', 2);
                }
                else
                {
                    $tr->addCell('R$ ' . number_format($totalgeral, 2, ',', '.'), 'right', 'datan', 2);    
                }
                $tr->addRow();
                $tr->addRow();
                $tr->addCell(date('d/m/Y h:i'), 'center', 'footer', 5);                
                // stores the file
                if (!file_exists("app/output/Movbancaria.{$format}") OR is_writable("app/output/Movbancaria.{$format}"))
                {
                    $tr->save("app/output/Movbancaria.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Movbancaria.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Movbancaria.{$format}");
                
                // shows the success message
                //new TMessage('info', 'Report generated. Please, enable popups.');
            }
            else
            {
                new TMessage('error', 'Nenhum registro foi encontrado');
            }
    
            // fill the form with the active record data
            $formdata->saldo = number_format($formdata->saldo, 2, ',', '.');
            $this->form->setData($formdata);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    public static function onExitContacorrente( $param )
    {
        try
        {
            TTransaction::open(TSession::getValue('banco'));
        
            $conta = new Contacorrente($param['contacorrente_id']);
                
            $obj = new StdClass;
            $obj->contacorrente = $conta->descricao;
                    
            TForm::sendData('form_Movbancaria_report', $obj);
                
            TTransaction::close();   
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', 'Conta não cadastrada...');    
        }
    }
}
?>