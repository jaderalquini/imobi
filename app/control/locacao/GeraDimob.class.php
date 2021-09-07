<?php
/**
 * BemForm Form
 * @author  <your name here>
 */
class GeraDimob extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::include_js('app/lib/include/application.js');
        
        // creates the form
        $this->form = new TQuickForm('form_GeraDimob');
        $this->form->class = 'tform'; // change CSS class        
        $this->form->style = 'display: table;width:100%'; // change style
        //$this->form->setFormTitle('Bem');
        $this->form->setFieldsByRow(2);
        
        // create the form fields
        $ano = new TEntry('ano');
        $retificadora = new TCombo('retificadora');
        $numrecibo = new TEntry('numrecibo');
        $especial = new TCombo('especial');
        $dataevento = new TDate('dataevento');
        $evento = new TCombo('evento');
        
        $retificadora->addItems(array('0' => 'Não', '1' => 'Sim'));
        $especial->addItems(array('0' => 'Não', '1' => 'Sim'));
        $evento->addItems(array('00' => 'Normal', '01' => 'Extinção', '02' => 'Fusão', '03' => 'Incorporação/Incorpordora', '04' => 'Cisão Total'));
        
        $retificadora->setValue(0);
        $especial->setValue(0);
        $evento->setValue('00');
        
        $dataevento->setMask('dd/mm/yyyy');
        
        $ano->setValue(date('Y') - 1);
        
        /*$ano->addValidation('Ano-calendário', new TRequiredValidator);
        $retificadora->addValidation('Declaração Retificadora', new TRequiredValidator);
        $especial->addValidation('Situação Especial', new TRequiredValidator);*/
        
        $this->form->addQuickField('Ano-calendário', $ano, 50);
        $this->form->addQuickField('Declaração Retificadora', $retificadora, 50);
        $this->form->addQuickField('Número Recibo', $numrecibo);
        $this->form->addQuickField('Situação Especial', $especial, 50);
        $this->form->addQuickField('Data do Evento', $dataevento, 100);
        $this->form->addQuickField('Evento', $evento);
         
        // create the form actions
        $this->form->addQuickAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onGenerate( $param )
    {
        try
        {
            $data = $this->form->getData();
            
            $ano = $param['ano'] != '' ? $param['ano'] : date('Y') - 1;
            $param['ano = $ano'];
            $retificadora = $param['retificadora'] != '' ? $param['retificadora'] : '0';
            $param['retificadora'] = $retificadora;
            $numrecibo = str_pad($param['numrecibo'],10,'0',STR_PAD_LEFT);
            $especial = $param['especial'] != '' ? $param['especial'] : '0';
            $param['especial'] = $especial;
            $dtevento = str_pad(FuncoesExtras::tirarFormatacao($param['dataevento']),8,'0',STR_PAD_RIGHT);
            $evento = str_pad($param['evento'],2,'0',STR_PAD_RIGHT);
            
            //self::validate($param);
            
            $reservado = str_pad('',369,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            $eof = FuncoesExtras::hexToStr('0D0A');
            $linha = 'DIMOB'.$reservado.$eof;
            
            //echo $linha."<br>";
            
            $file = 'tmp/dimob.txt';
            $fp = fopen($file, "w+");
            
            //fwrite($fp, $linha);
            //fwrite($fp, strlen($linha));
            
            TTransaction::open('permission');
            $empresa = new Empresa(TSession::getValue('empresa'));
            TTransaction::close();
            
            if ($empresa->cnpj)
            {
                $cnpj = FuncoesExtras::tirarFormatacao($empresa->cnpj);
            }
            else
            {
                $this->form->setData($data);
                throw new Exception('Por favor informe o CNPJ da empresa na tela de Cadastro de Empresas.');
            }
            
            if ($empresa->nome)
            {
                $nome = str_pad(FuncoesExtras::tirarAcentos($empresa->nome),60,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            }
            else
            {
                $this->form->setData($data);
                throw new Exception('Por favor informe o nome da empresa na tela de Cadastro de Empresas.');
            }
            
            
            if ($empresa->responsavel_cpf)
            {
                $cpf = str_pad(FuncoesExtras::tirarFormatacao($empresa->responsavel_cpf),11,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            }
            else
            {
                $this->form->setData($data);
                throw new Exception('Por favor informe o CPF do responsável da empresa na tela de Cadastro de Empresas.');
            }
            
            if ($empresa->endereco)
            {
                $endereco = str_pad(FuncoesExtras::tirarAcentos($empresa->endereco),120,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            }
            else
            {
                $this->form->setData($data);
                throw new Exception('Por favor informe o endereço da empresa na tela de Cadastro de Empresas.');
            }
            
            if ($empresa->uf_id)
            {
                $uf= $empresa->uf_id;
            }
            else
            {
                $this->form->setData($data);
                throw new Exception('Por favor informe o UF da empresa na tela de Cadastro de Empresas.');
            }
            
            if ($empresa->municipio_id)
            {
                $codmunicipio= $empresa->municipio_id;
            }
            else
            {
                $this->form->setData($data);
                throw new Exception('Por favor informe o código do município da empresa na tela de Cadastro de Empresas.');
            }
            
            TTransaction::open(TSession::getValue('banco'));
            $conn = TTransaction::get();
            
            $reservado = str_pad('',20,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            $reservado2 = str_pad('',10,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            	
            $linha .= 'R01'.$cnpj.$ano.$retificadora.$numrecibo.$especial.$dtevento.$evento.$nome.$cpf.$endereco.
                     $uf.$codmunicipio.$reservado.$reservado2.$eof;
                     
            //echo $linha."<br>";
                     
            //fwrite($fp, $linha);
            //fwrite($fp, strlen($linha));
            
            $query = "select distinct pr.contrato_id nrcontrato, c.dtcadastro dtcontrato,". 
                     "c1.cpfcnpj cpfcnpj1, c1.nome nome1, c2.cpfcnpj cpfcnpj2, c2.nome nome2,".
                     "(select pr2.valor from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='01') valor1,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='02') valor2,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='03') valor3,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='04') valor4,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='05') valor5,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='06') valor6,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='07') valor7,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='08') valor8,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='09') valor9,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='10') valor10,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='11') valor11,".
                     "(select sum(pr2.valor) from parcela_receber pr2 WHERE pr2.contrato_id = pr.contrato_id and strftime('%Y', pr2.dtpagto)='".$ano."' and strftime('%m', pr2.dtpagto)='12') valor12,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='01') comissao1,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='02') comissao2,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='03') comissao3,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='04') comissao4,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='05') comissao5,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='06') comissao6,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='07') comissao7,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='08') comissao8,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='09') comissao9,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='10') comissao10,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='11') comissao11,".
                     "(select sum(pg.vlcomissao) from parcela_pagar pg WHERE pg.contrato_id = pr.contrato_id AND strftime('%Y', pg.dtpagto)='".$ano."' and strftime('%m', pg.dtpagto)='12') comissao12,".
                     "b.urbrural tipo, b.endereco endereco, b.cep cep, b.municipio_id codmunicipio, b.uf_id uf ".
                     "from parcela_receber pr ".
                     "left join contrato c on c.id = pr.contrato_id ".
                     "left join bem b on b.id = pr.bem_id ".
                     "left join cliente c1 on c1.id=b.proprietario_id ".
                     "left join cliente c2 on c2.id = pr.cliente_id ".
                     "where dtpagto >= '".$ano."-01-01' and dtpagto <= '".$ano."-12-31'";
            
            //echo $query."<br>";
            $results = $conn->query($query);
            
            if ($results)
            {
                $cont = 0;
                foreach ($results as $result)
                {
                    $cont++;
                    $seq = str_pad($cont,5,'0',STR_PAD_RIGHT);
                    $cpfcnpj1 = str_pad(FuncoesExtras::tirarFormatacao($result['cpfcnpj1']),14,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
                    $nome1 = str_pad(FuncoesExtras::tirarAcentos($result['nome1']),60,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
                    $cpfcnpj2 = str_pad(FuncoesExtras::tirarFormatacao($result['cpfcnpj2']),14,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
                    $nome2 = str_pad(FuncoesExtras::tirarAcentos($result['nome2']),60,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
                    $nrcontrato = str_pad($result['nrcontrato'],6,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            	    $dtcontrato = FuncoesExtras::tirarFormatacao(TDate::date2br($result['dtcontrato']));
            	    $tipo = $result['tipo'];
            	    $endereco = str_pad(FuncoesExtras::tirarAcentos($result['endereco']), 60, FuncoesExtras::hexToStr('20'), STR_PAD_RIGHT);
            	    $cep = FuncoesExtras::tirarFormatacao($result['cep']);
            	    $codmunicipio = $result['codmunicipio'];
            	    $uf = strtoupper($result['uf']);
            	    
            	    $linha .= 'R02'.$cnpj.$ano.$seq.$cpfcnpj1.$nome1.$cpfcnpj2.$nome2.$nrcontrato.$dtcontrato;
            	    
            	    $valor1 = $result['valor1'];
                	$valor1 = explode('.',$valor1);
            	    $valor1 = str_pad($valor1[0],12,'0',STR_PAD_LEFT).str_pad($valor1[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor2 = $result['valor2'];
                	$valor2 = explode('.',$valor2);
            	    $valor2 = str_pad($valor2[0],12,'0',STR_PAD_LEFT).str_pad($valor2[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor3 = $result['valor3'];
                	$valor3 = explode('.',$valor3);
            	    $valor3 = str_pad($valor3[0],12,'0',STR_PAD_LEFT).str_pad($valor3[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor4 = $result['valor4'];
                	$valor4 = explode('.',$valor4);
            	    $valor4 = str_pad($valor4[0],12,'0',STR_PAD_LEFT).str_pad($valor4[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor5 = $result['valor5'];
                	$valor5 = explode('.',$valor5);
            	    $valor5 = str_pad($valor5[0],12,'0',STR_PAD_LEFT).str_pad($valor5[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor6 = $result['valor6'];
                	$valor6 = explode('.',$valor6);
            	    $valor6 = str_pad($valor6[0],12,'0',STR_PAD_LEFT).str_pad($valor6[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor7 = $result['valor7'];
                	$valor7 = explode('.',$valor7);
            	    $valor7 = str_pad($valor7[0],12,'0',STR_PAD_LEFT).str_pad($valor7[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor8 = $result['valor8'];
                	$valor8 = explode('.',$valor8);
            	    $valor8 = str_pad($valor8[0],12,'0',STR_PAD_LEFT).str_pad($valor8[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor9 = $result['valor9'];
                	$valor9 = explode('.',$valor9);
            	    $valor9 = str_pad($valor9[0],12,'0',STR_PAD_LEFT).str_pad($valor9[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor10 = $result['valor10'];
                	$valor10 = explode('.',$valor10);
            	    $valor10 = str_pad($valor10[0],12,'0',STR_PAD_LEFT).str_pad($valor10[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor11 = $result['valor11'];
                	$valor11 = explode('.',$valor11);
            	    $valor11 = str_pad($valor11[0],12,'0',STR_PAD_LEFT).str_pad($valor11[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $valor12 = $result['valor12'];
                	$valor12 = explode('.',$valor12);
            	    $valor12 = str_pad($valor12[0],12,'0',STR_PAD_LEFT).str_pad($valor12[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao1 = $result['comissao1'];
                	$comissao1 = explode('.',$comissao1);
            	    $comissao1 = str_pad($comissao1[0],12,'0',STR_PAD_LEFT).str_pad($comissao1[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao2 = $result['comissao2'];
                	$comissao2 = explode('.',$comissao2);
            	    $comissao2 = str_pad($comissao2[0],12,'0',STR_PAD_LEFT).str_pad($comissao2[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao3 = $result['comissao3'];
                	$comissao3 = explode('.',$comissao3);
            	    $comissao3 = str_pad($comissao3[0],12,'0',STR_PAD_LEFT).str_pad($comissao3[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao4 = $result['comissao4'];
                	$comissao4 = explode('.',$comissao4);
            	    $comissao4 = str_pad($comissao4[0],12,'0',STR_PAD_LEFT).str_pad($comissao4[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao5 = $result['comissao5'];
                	$comissao5 = explode('.',$comissao5);
            	    $comissao5 = str_pad($comissao5[0],12,'0',STR_PAD_LEFT).str_pad($comissao5[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao6 = $result['comissao6'];
                	$comissao6 = explode('.',$comissao6);
            	    $comissao6 = str_pad($comissao6[0],12,'0',STR_PAD_LEFT).str_pad($comissao6[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao7 = $result['comissao7'];
                	$comissao7 = explode('.',$comissao7);
            	    $comissao7 = str_pad($comissao7[0],12,'0',STR_PAD_LEFT).str_pad($comissao7[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao8 = $result['comissao8'];
                	$comissao8 = explode('.',$comissao8);
            	    $comissao8 = str_pad($comissao8[0],12,'0',STR_PAD_LEFT).str_pad($comissao8[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao9 = $result['comissao9'];
                	$comissao9 = explode('.',$comissao9);
            	    $comissao9 = str_pad($comissao9[0],12,'0',STR_PAD_LEFT).str_pad($comissao9[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao10 = $result['comissao10'];
                	$comissao10 = explode('.',$comissao10);
            	    $comissao10 = str_pad($comissao10[0],12,'0',STR_PAD_LEFT).str_pad($comissao10[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao11 = $result['comissao11'];
                	$comissao11 = explode('.',$comissao11);
            	    $comissao11 = str_pad($comissao11[0],12,'0',STR_PAD_LEFT).str_pad($comissao11[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $comissao12 = $result['comissao12'];
                	$comissao12 = explode('.',$comissao12);
            	    $comissao12 = str_pad($comissao12[0],12,'0',STR_PAD_LEFT).str_pad($comissao12[1],2,'0',STR_PAD_RIGHT);
            	    
            	    $imposto = str_pad('0',14,'0',STR_PAD_RIGHT);
            	    
            	    $linha .= $valor1.$comissao1.$imposto.$valor2.$comissao2.$imposto.$valor3.$comissao3.$imposto.$valor4.$comissao4.$imposto.
            	            $valor5.$comissao5.$imposto.$valor6.$comissao6.$imposto.$valor7.$comissao7.$imposto.$valor8.$comissao8.$imposto.
            	            $valor9.$comissao9.$imposto.$valor10.$comissao10.$imposto.$valor11.$comissao11.$imposto.$valor12.$comissao12.$imposto;
            	    
            	    $linha .= $tipo.$endereco.$cep.$codmunicipio.$reservado.$uf.$reservado2.$eof;
            	    
            	    //echo $linha."<br>";
                    
                    //fwrite($fp, $linha);
            	    //fwrite($fp, strlen($linha)); 
                }
            }
            
            $reservado = str_pad('',100,FuncoesExtras::hexToStr('20'),STR_PAD_RIGHT);
            
            $linha .= 'T9'.$reservado.$eof; 
            
            //echo $linha."<br>";
            
            fwrite($fp, $linha);
            //fwrite($fp, strlen($linha));
            
            fclose($fp);            
            TTransaction::close();
            
            $this->form->setData($data);
            
            parent::openFile("tmp/dimob.txt");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    function validate($param)
    {
        $this->form->validate($param);
        
        if ($param['retificadora']=='1')
        {
            $validator = new TRequiredValidator;
            $validator->validate('Número Recibo', $data->numrecibo);
        }
        
        if ($param['especial']=='1')
        {
            $validator = new TDateValidator;
            $validator('Data do Evento', $data->dataevento);
            
            $validator = new TRequiredValidator;
            $validator->validate('Evento', $data->evento);
        }
    }
}