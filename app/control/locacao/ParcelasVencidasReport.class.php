<?php
/**
 * ParcelaReceberReport Report
 * @author  <your name here>
 */
class ParcelasVencidasReport extends TPage
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
        $this->form = new TQuickForm('form_ParcelaReceber_report');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('ParcelaReceber Report'); 

        // create the form fields
        $ordem = new TRadioGroup('ordem');
        $dtvenctode = new TDate('dtvenctode');
        $dtvenctoate = new TDate('dtvenctoate');
        $dtprocesso = new TDate('dtprocesso');
        $output_type = new THidden('output_type');
        
        // Máscaras
        $dtvenctode->setMask('dd/mm/yyyy');
        $dtvenctoate->setMask('dd/mm/yyyy');
        $dtprocesso->setMask('dd/mm/yyyy');
        
        // Adiciona validação aos campos
        $ordem->addValidation('Ordem', new TRequiredValidator);
        $dtvenctode->addValidation('Vencidas de', new TRequiredValidator);
        $dtvenctode->addValidation('Vencidas de', new TDateValidator);
        $dtvenctoate->addValidation('Vencidas até', new TRequiredValidator);
        $dtvenctoate->addValidation('Vencidas até', new TDateValidator);
        $dtprocesso->addValidation('Processando', new TRequiredValidator);
        $dtprocesso->addValidation('Processando', new TDateValidator);
        //$output_type->addValidation('Tipo de Saída', new TRequiredValidator);
        
        // Adiciona Items aos campos RadioGroup e ComboBox
        $ordem->addItems(array('cliente' => 'Alfabética', 'dtvencto' => 'Data de Vencimento'));
        //$output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));
        
        // Adiciona Valor Padrão aos Campos
        $ordem->setValue('dtvencto');
        $dtvenctode->setValue(date('d/m/Y'));
        $dtvenctoate->setValue(date('d/m/Y'));
        $dtprocesso->setValue(date('d/m/Y'));
        $output_type->setValue('pdf');
        
        // Define Layout dos campos RadioGroup e ComboBox
        $ordem->setLayout('horizontal');
        //$output_type->setLayout('horizontal');

        // add the fields
        $this->form->addQuickField('Ordem', $ordem, 200);
        $this->form->addQuickField('Vencidas de', $dtvenctode, 100);
        $this->form->addQuickField('Vencidas até', $dtvenctoate, 100);
        $this->form->addQuickField('Processndo', $dtprocesso, 100);
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
            TTransaction::open(TSession::getValue('banco'));
            $conn = TTransaction::get();
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
            $query = "SELECT distinct c.id id, cli.nome nomecliente, pr.contrato_id contrato, numero, dtvencto, pr.valor, cli.fone fone, p.nome nomeproprietario
                      from parcela_receber pr
                      left join contrato c on c.id = pr.contrato_id
                      left join cliente cli on cli.id = c.cliente_id
                      left join bem b on b.id = pr.bem_id
                      left join cliente p on p.id = b.proprietario_id where";
            $and = FALSE;
            
            if ($formdata->dtvenctode)
            {
                $formdata->dtvenctode = TDate::date2us($formdata->dtvenctode);
                $query .= " dtvencto >= '" . $formdata->dtvenctode . "'";
                $and = TRUE;
                $formdata->dtvenctode = TDate::date2br($formdata->dtvenctode);
            }
            if ($formdata->dtvenctoate)
            {
                $formdata->dtvenctoate = TDate::date2us($formdata->dtvenctoate);
                $query .= $and == TRUE ? " and " : " ";
                $query .= "dtvencto <= '" . $formdata->dtvenctoate . "'";
                $and = TRUE;
                $formdata->dtvenctoate = TDate::date2br($formdata->dtvenctoate);
            }
            if ($formdata->dtprocesso)
            {
                $formdata->dtprocesso = TDate::date2us($formdata->dtprocesso);
                $query .= $and == TRUE ? " and " : " ";
                $query .= "(dtpagto == '0' or dtpagto > '" . $formdata->dtprocesso . "')";
                $formdata->dtprocesso = TDate::date2br($formdata->dtprocesso);
            }
            if ($formdata->ordem)
            {
                $query .= " order by " . $formdata->ordem;
            }
            
            $results = $conn->query($query);
            $format  = $formdata->output_type;
            
            if ($results)
            {
                $designer = new TReport();
                $designer->setTitle('Relação de Parcelas Vencidas');
                $designer->setFiltro('PERÍODO DE: '.$formdata->dtvenctode.' ATÉ '.$formdata->dtvenctoate);
                $columns = array();
                $columns[0]['size'] = 40;
                $columns[0]['text'] = 'CODCLI';
                $columns[0]['align'] = 'R';
                $columns[1]['size'] = 120;
                $columns[1]['text'] = 'LOCATÁRIO';
                $columns[1]['align'] = 'L';
                $columns[2]['size'] = 40;
                $columns[2]['text'] = 'CONTRATO';
                $columns[2]['align'] = 'R';
                $columns[3]['size'] = 30;
                $columns[3]['text'] = 'PAR';
                $columns[3]['align'] = 'R';
                $columns[4]['size'] = 55;
                $columns[4]['text'] = 'DT.VENCTO';
                $columns[4]['align'] = 'R';
                $columns[5]['size'] = 60;
                $columns[5]['text'] = 'VALOR';
                $columns[5]['align'] = 'R';
                $columns[6]['size'] = 45;
                $columns[6]['text'] = 'ATRAZO';
                $columns[6]['align'] = 'R';
                $columns[7]['size'] = 45;
                $columns[7]['text'] = 'TELEFONE';
                $columns[7]['align'] = 'R';
                $columns[8]['size'] = 120;
                $columns[8]['text'] = 'LOCADOR';
                $columns[8]['align'] = 'L';
                $designer->setColumns($columns);
                $designer->generate();
                $designer->SetMargins(30,30,30);
                $designer->SetAutoPageBreak(true, 30);
            	$designer->SetFont('Arial','',8);
            	$designer->SetX(30);
            	
            	$i = 0;
            	$total = 0;
            	foreach ($results as $result)
            	{
            	    $i++;
            	    $designer->Cell(40,10,number_format($result['id'],0,',','.'),0,0,'R');
            	    $designer->Cell(120,10,substr($result['nomecliente'],0,20),0,0,'L');
            	    $designer->Cell(40,10,number_format($result['contrato'],0,',','.'),0,0,'R');
            	    $designer->Cell(30,10,$result['numero'],0,0,'R');
            	    $designer->Cell(55,10,TDate::date2br($result['dtvencto']),0,0,'R');
            	    $designer->Cell(60,10,number_format($result['valor'],2,',','.'),0,0,'R');
            	    $designer->Cell(45,10,FuncoesExtras::dateDifDias($formdata->dtprocesso,TDate::date2br($result['dtvencto'])),0,0,'R');
            	    $designer->Cell(45,10,$result['fone'],0,0,'R');
            	    $designer->Cell(120,10,substr($result['nomeproprietario'],0,20),0,0,'L');
            	    $total = $total + $result['valor'];
            	    $designer->Ln();
            	}
            	
            	$designer->SetFont('Arial','B',8);
            	$h = $designer->GetY();
            	$designer->Line(30, $h, 565, $h);
            	$designer->Ln();
            	$designer->Cell(285,10,'Total Vencido',0,0,'L');
            	$designer->Cell(60,10,number_format($total,2,',','.'),0,0,'R');
            	$designer->Line(30, $h + 25, 565, $h + 25);
            	
            	if ($i == 0)
            	{
            	    new TMessage('error', 'Nenhum registro foi encontrado');
            	}
            	else
            	{               
                    if (!file_exists("app/output/PrevisaoPagtoLocador.{$format}") OR is_writable("app/output/PrevisaoPagtoLocador.{$format}"))
                    {
                        $designer->save("app/output/PrevisaoPagtoLocador.{$format}");
                    }
                    else
                    {
                        throw new Exception(_t('Permission denied') . ': ' . "app/output/PrevisaoPagtoLocador.{$format}");
                    }
                    
                    // open the report file
                    parent::openFile("app/output/PrevisaoPagtoLocador.{$format}");
                }
            }
            else
            {
                new TMessage('error', 'Nenhum registro foi encontrado');
            }
    
            // fill the form with the active record data
            $this->form->setData($formdata);
            
            // close the transaction
            TTransaction::close();
    
            // fill the form with the active record data
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
}
