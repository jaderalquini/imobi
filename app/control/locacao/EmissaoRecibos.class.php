<?php
class EmissaoRecibos extends TPage
{
    protected $form; // form
    
    public function __construct( $param )
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_EmissaoRecibos');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
                
        $recibo = new TEntry('recibo');
        $valor = new TEntry('valor');
        $pagador = new TEntry('pagador');
        $valor_extenso = new TText('valor_extenso');
        $origem = new TText('origem');
        $cidade = new TEntry('cidade');
        $dia = new TEntry('dia');
        $mes = new TEntry('mes');
        $ano = new TEntry('ano');
        $assinatura = new TEntry('assinatura');
        
        TTransaction::open('permission');            
        $empresa = new Empresa(TSession::getValue('empresa'));
        TTransaction::close();
        
        $this->form->addQuickFields(new TLabel('Recibo nº.'), array($recibo, new TLabel('Valor'), $valor));
        $this->form->addQuickField(new TLabel('Recebi(emos) do(e)'), $pagador);
        $this->form->addQuickField(new TLabel('A Quantia de'), $valor_extenso);
        $this->form->addQuickField(new TLabel('Proveniente de'), $origem);
        $this->form->addQuickFields(new TLabel('Local e Data'), array($cidade, $dia, new TLabel('de'), $mes, new TLabel('de'), $ano));
        $this->form->addQuickField(new TLabel('Assinatura'), $assinatura);
        
        $recibo->setSize(100);
        $valor->setSize(300);
        $pagador->setSize(600);
        $valor_extenso->setSize(600, 100);
        $origem->setSize(600, 50);
        $cidade->setSize(200);
        $dia->setSize(25);
        $mes->setSize(100);
        $ano->setSize(50);
        $assinatura->setSize(600);
        
        $recibo->addValidation('Recibo', new TRequiredValidator);
        $pagador->addValidation('Pagador', new TRequiredValidator);
        $valor_extenso->addValidation('Valor por Extenso', new TRequiredValidator);
        $origem->addValidation('Proveniente', new TRequiredValidator);
        $cidade->addValidation('Cidade', new TRequiredValidator);
        $dia->addValidation('Dia', new TRequiredValidator);
        $mes->addValidation('Mês', new TRequiredValidator);
        $ano->addValidation('Ano', new TRequiredValidator);
        $assinatura->addValidation('Assinatura', new TRequiredValidator);
        
        $valor->setExitAction(new TAction(array($this, 'onExitValor')));
        
        $valor->setNumericMask(2, ',', '.');
        
        TTransaction::open(TSession::getValue('banco'));
        
        $repository = new TRepository('Sequencia');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id','=','1'));
        $objects = $repository->load($criteria, FALSE);
        
        if ($objects)
        {
            foreach($objects as $object)
            {
                $numrecibo = $object->numrecibo;
            }
        }
        
        TTransaction::close();
                
        $recibo->setValue($numrecibo + 1);
        $pagador->setValue($empresa->nome);
        $cidade->setValue('JARAGUÁ DO SUL');
        $dia->setValue(date('d'));
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        $mes->setValue(strtoupper(strftime('%B', strtotime('today'))));
        $ano->setValue(date('Y'));
        $assinatura->setValue($empresa->nome);
        
        $this->form->addQuickAction(_t('Print'), new TAction(array($this, 'onInputDialog')), 'fa:print');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'EmissaoRecibos'));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    public function onInputDialog( $param )
    {
        $form = new TQuickForm('input_form');
        $form->style = 'padding:20px';
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('
            $(document).ready(function() {
                $("input[name=copias]").focus();
            });
        ');
        parent::add($script);
        
        $copias = new TEntry('copias');
        $recibo = new THidden('recibo');
        $valor = new THidden('valor');
        $pagador = new THidden('pagador');
        $valor_extenso = new THidden('valor_extenso');
        $origem = new THidden('origem');
        $cidade = new THidden('cidade');
        $dia = new THidden('dia');
        $mes = new THidden('mes');
        $ano = new THidden('ano');
        $assinatura = new THidden('assinatura');
        
        $form->addQuickField('Número de Cópias', $copias, 50);
        $form->addQuickField('', $recibo);
        $form->addQuickField('', $valor);
        $form->addQuickField('', $pagador);
        $form->addQuickField('', $valor_extenso);
        $form->addQuickField('', $origem);
        $form->addQuickField('', $cidade);
        $form->addQuickField('', $dia);
        $form->addQuickField('', $mes);
        $form->addQuickField('', $ano);
        $form->addQuickField('', $assinatura);
        
        $copias->setValue('1');
        $recibo->setValue($param['recibo']);
        $valor->setValue($param['valor']);
        $pagador->setValue($param['pagador']);
        $valor_extenso->setValue($param['valor_extenso']);
        $origem->setValue($param['origem']);
        $cidade->setValue($param['cidade']);
        $dia->setValue($param['dia']);
        $mes->setValue($param['mes']);
        $ano->setValue($param['ano']);
        $assinatura->setValue($param['assinatura']);
        
        $form->addQuickAction('Confirmar', new TAction(array($this, 'onPrint')), 'fa:check-circle green');
        
        // show the input dialog
        new TInputDialog('Cópias', $form);
    }
    
    public function onPrint($param)
    {
        //new TMessage('info', json_encode($param));
        
        TTransaction::open('permission');            
        $empresa = new Empresa(TSession::getValue('empresa'));
        TTransaction::close();
        
        TTransaction::open(TSession::getValue('banco'));           
        $uf = new UF($empresa->uf_id);
        
        $object = new Recibo();
        $object->id = $param['recibo'];
        $object->valor = FuncoesExtras::retiraFormatacao($param['valor']);
        $object->recebedor = $param['assinatura'];
        $object->descricao = $param['origem'];
        $object->pagador = $param['pagador'];
        $object->dtrecibo = date('Y-m-d');
        $object->store();
        
        $object = new Sequencia(1);
        $sequencia = array();
        $sequencia['numrecibo'] = $param['recibo'];
        $object->fromArray( (array) $sequencia );
        $object->store();
        
        $repository = new TRepository('Sequencia');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id','=','1'));
        $objects = $repository->load($criteria, FALSE);
        
        if ($objects)
        {
            foreach($objects as $object)
            {
                $numrecibo = $object->numrecibo;
            }
        }
        
        $obj = new StdClass;
        $obj->recibo = $numrecibo + 1;
            
        TForm::sendData('form_EmissaoRecibos', $obj);
        
        TTransaction::close();
        $empresaInf1 = "CNPJ N°." . $empresa->cnpj . " - Registro JUCESC N°. " . $empresa->nrjucesc . " - CRECI N°. " . $empresa->creci;
        $empresaInf2 = $empresa->endereco . " - " . $empresa->bairro;
        $empresaInf3 = $empresa->municipio . " - " . $uf->nome . " - CEP " . $empresa->cep;
        
        $designer = new TPDFDesigner;
        $designer->generate();
        $designer->SetMargins(30,30,30);
        $designer->SetAutoPageBreak(true, 30);
        
        $copias = $param['copias'];
        for ($i = 1; $i <= $copias; $i++)
        {
            $x = $i - 1;
            
            $logo = $empresa->logo;
            $designer->Image($logo, 30, 20 + 400 * $x, 70, 70);
            $designer->SetTextColor(0,0,128);
            $designer->SetFont('Arial','B',18);
            $designer->SetXY(110, 20 + 400 * $x);
            $designer->Write(18, utf8_decode($empresa->nome));
            $designer->SetFont('Arial','',14);
        	$designer->SetTextColor(0,0,0);
        	$designer->SetX(450);
        	
        	$designer->SetTextColor(0,0,128);
            $designer->SetFont('Arial','',10);
            $designer->Ln();
            $designer->SetX(110);
            $designer->Write(10, utf8_decode($empresaInf1));
            $designer->Ln();
            $designer->SetX(110);
            $designer->Write(10, utf8_decode($empresaInf2));
            $designer->Ln();
            $designer->SetX(110);
            $designer->Write(10, utf8_decode($empresaInf3));
            $designer->Ln();
            $designer->SetX(110);
            $designer->Write(10, "Fone/Fax: " . $empresa->fone);
            $designer->Ln();
            $designer->SetX(110);
            $designer->SetTextColor(0,0,75);
            $designer->Write(10, $empresa->site . " - E-mail:" . $empresa->email);
            $designer->Ln();
            $designer->Ln();
            $designer->SetDrawColor(204,51,0);
            $designer->Line(30, 95 + 400 * $x, 555, 95 + 400 * $x);
            
            $designer->SetFont('Arial','',14);
            $designer->SetTextColor(0,0,0);
            $designer->Ln();
            $designer->Ln();
            $numrecibo = number_format($param['recibo'], 0, ",", ".");
            $designer->Write(15, utf8_decode('Recibo nº.: ') . $numrecibo);
            $designer->SetX(400);
            $designer->Write(15, utf8_decode('Valor: R$ ') . $param['valor']);
            $designer->Ln();
            $designer->Ln();
            $designer->Write(15, utf8_decode('Recebi(emos) do(e) ' . $param['pagador']));
            $designer->Ln();
            $designer->Ln();
            $designer->Write(15, utf8_decode('A Quantia de '));
            $designer->SetX(120);
            $designer->Write(15, utf8_decode(str_pad(substr($param['valor_extenso'], 0, 50), 50, "*", STR_PAD_RIGHT)));
            $designer->Ln();
            $designer->SetX(120);
            $designer->Write(15, utf8_decode(str_pad(substr($param['valor_extenso'], 50, 50), 50, "*", STR_PAD_RIGHT)));
            $designer->Ln();
            $designer->SetX(120);
            $designer->Write(15, utf8_decode(str_pad(substr($param['valor_extenso'], 100, 50), 50, "*", STR_PAD_RIGHT)));
            $designer->Ln();
            $designer->Ln();
            $designer->Write(15, utf8_decode('Proveniente de ' . substr($param['origem'], 0, 50)));
            $designer->Ln();
            $designer->SetX(125);
            $designer->Write(15, utf8_decode(substr($param['origem'], 50, 50)));
            $designer->Ln();
            $designer->Ln();
            $designer->MultiCell(500, 15, utf8_decode($param['cidade'] . ', ' . $param['dia'] . ' de ' . $param['mes'] . ' de ' . $param['ano']), 0, 'C');
            $designer->Ln();
            $designer->Ln();
            $designer->Ln();
            $designer->Ln();
            $designer->MultiCell(500, 15, utf8_decode($param['assinatura']), 0, 'C');
            $designer->SetX(200);
            $designer->SetDrawColor(0,0,0);
            $designer->Line(150, 350 + 400 * $x, 400, 350 + 400 * $x);
        }
        
        $file = 'app/output/Recibo.pdf';
                
        if (!file_exists($file) OR is_writable($file))
        {
            $designer->save($file);
            parent::openFile($file);
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $file);
        }
    }
    
    public static function onExitValor($param)
    {
        //new TMessage('info', json_encode($param));
        $valor = $param['valor'];
        //$valor = '999,99';
        try
        {
            $obj = new StdClass;
            $valor = FuncoesExtras::valorPorExtenso(FuncoesExtras::retiraFormatacao($valor));
            $obj->valor_extenso = strtoupper($valor);
            
            TForm::sendData('form_EmissaoRecibos', $obj);
        }
        catch (Exception $e)
        {
            
        }
    }
}
?>