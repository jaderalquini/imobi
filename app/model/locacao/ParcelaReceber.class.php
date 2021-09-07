<?php
/**
 * ParcelaReceber Active Record
 * @author  <your-name-here>
 */
class ParcelaReceber extends TRecord
{
    const TABLENAME = 'parcela_receber';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
        
    private $bem;
    private $cliente;
    private $cliente2;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('contrato_id');
        parent::addAttribute('numero');
        parent::addAttribute('sequencia');
        parent::addAttribute('bem_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('dtinicio');
        parent::addAttribute('dtfim');
        parent::addAttribute('dtvencto');
        parent::addAttribute('valor');
        parent::addAttribute('vlacrescido');
        parent::addAttribute('dtpagto');
        parent::addAttribute('numrecibo');
        parent::addAttribute('vlpago');
        parent::addAttribute('opjuros');
        parent::addAttribute('vljuros');
        parent::addAttribute('opmulta');
        parent::addAttribute('vlmulta');
        parent::addAttribute('opseguro');
        parent::addAttribute('vlseguro');
        parent::addAttribute('opcondominio');
        parent::addAttribute('vlcondominio');
        parent::addAttribute('opluz');
        parent::addAttribute('vlluz');
        parent::addAttribute('opagua');
        parent::addAttribute('vlagua');
        parent::addAttribute('opiptu');
        parent::addAttribute('vliptu');
        parent::addAttribute('numrecpro');
        parent::addAttribute('opdesc');
        parent::addAttribute('vldesc');
        parent::addAttribute('opgas');
        parent::addAttribute('vlgas');
        parent::addAttribute('cliente2_id');
        parent::addAttribute('observacao');
    }
    
    /**
     * Method get_bem
     * Sample of usage: $parcela_receber->bem->attribute;
     * @returns Bem instance
     */
    public function get_bem()
    {
        try
        {
            $obj = new Bem($this->bem_id);
            return $obj->descricao;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $parcela_receber->cliente->attribute;
     * @returns Cliente instance
     */
    public function get_cliente()
    {
        try
        {
            $obj = new Cliente($this->cliente_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;
        }
    }   
    
    /**
     * Method get_cliente2
     * Sample of usage: $contrato->cliente2->attribute;
     * @returns Cliente instance
     */
    public function get_cliente2()
    {
        try
        {
            $obj = new Cliente($this->cliente2_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;    
        }
    }
    
    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $ppagar = new TRepository('ParcelaPagar');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('contrato_id', '=', $this->contrato_id));
        $criteria->add(new TFilter('numero', '=', $this->numero));
        $ppagar->delete($criteria);
        
        $contrato = new Contrato($this->contrato_id);
        $bem = new Bem($contrato->bem_id);
        
        if ($this->numero == $contrato->qtdeparc)
        {
            $array = array();
            $array['liquidado'] = 'S';
            $contrato->fromArray((array)$array);
            $contrato->store();
                        
            $array = array();
            $array['cliente_id'] = 0;
            $array['contrato_id'] = 0;
            $array['cliente2_id'] = 0;
            $bem->fromArray((array)$array);
            $bem->store();
        }
                
        parent::delete($id);
    }
    
    public function onImport($data)
    {         
        $class = get_class($this);
        $table = constant("{$class}::TABLENAME");
        
        TTransaction::open(TSession::getValue('banco'));
        $conn = TTransaction::get();
        
        $sql = "delete from $table";
        
        $conn-> query($sql);
        
        $target_folder = 'tmp/CSV/';
        $target_file   = $target_folder . $class . '.csv';
        
        $sql = "INSERT INTO $table (id, contrato_id, numero, sequencia, bem_id, cliente_id, dtinicio, dtfim, 
                dtvencto, valor, vlacrescido, dtpagto, numrecibo, vlpago, opjuros, vljuros, opmulta, vlmulta, 
                opseguro, vlseguro, opcondominio, vlcondominio, opluz, vlluz, opagua, vlagua, opiptu, vliptu, 
                numrecpro, opdesc, vldesc, opgas, vlgas, cliente2_id) values ";
           
        $primeiro = TRUE;
        $i = 0;
        $j = 0;
        $id = 0;
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {   
            if ($i == 500)
            {
                try
                {
                    $conn-> query($sql);    
                }
                catch (Exception $e) // in case of exception
                {
                    $j++;                    
                    $target_folder = 'tmp/SQL/';
                    $target_file   = $target_folder . $class . $j . '.sql';
                    $fp = fopen($target_file, "w");                    
         
                    fwrite($fp, $sql);
                    
                    new TMessage('error', $e->getMessage());    
                }
            
                $i = 0;
                $primeiro = TRUE;
                $sql = "INSERT INTO $table (id, contrato_id, numero, sequencia, bem_id, cliente_id, dtinicio, dtfim, 
                        dtvencto, valor, vlacrescido, dtpagto, numrecibo, vlpago, opjuros, vljuros, opmulta, vlmulta, 
                        opseguro, vlseguro, opcondominio, vlcondominio, opluz, vlluz, opagua, vlagua, opiptu, vliptu, 
                        numrecpro, opdesc, vldesc, opgas, vlgas, cliente2_id) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($contrato_id, $numero, $sequencia, $bem_id, $cliente_id, $dtinicio, $dtfim, $dtvencto,
                $valor, $vlacrescido, $dtpagto, $numrecibo, $vlpago, $vljuros, $vlmulta, $vlseguro, $vlcondominio, $vlluz, 
                $vlagua, $vliptu, $numrecpro, $vldesc, $vlgas, $cliente2_id) =  $data;
                        
            $i++;
            $id++;
            $dtinicio = substr($dtinicio,0,4)."-".substr($dtinicio,4,2)."-".substr($dtinicio,6,2);
            $dtfim = substr($dtfim,0,4)."-".substr($dtfim,4,2)."-".substr($dtfim,6,2);
            $dtvencto = substr($dtvencto,0,4)."-".substr($dtvencto,4,2)."-".substr($dtvencto,6,2);
            $valor = intval($valor);
            $valor = $valor / 100;
            $vlacrescido = intval($vlacrescido);
            $vlacrescido = $vlacrescido / 100;
            if ($dtpagto != '0') 
            {
                $dtpagto = substr($dtpagto,0,4)."-".substr($dtpagto,4,2)."-".substr($dtpagto,6,2);                         
            }
            $vlpago = intval($vlpago);
            $vlpago = $vlpago / 100;
            $opjuros = '+';
            $vljuros = intval($vljuros);
            $vljuros = $vljuros / 100;
            $opmulta = '+';
            $vlmulta = intval($vlmulta);
            $vlmulta = $vlmulta / 100;
            $opseguro = substr($vlseguro, -1, 1);
            $vlseguro = substr($vlseguro, 0, -1);
            $vlseguro = intval($vlseguro);
            $vlseguro = $vlseguro / 100;
            $opcondominio = substr($vlcondominio, -1, 1);
            $vlcondominio = substr($vlcondominio, 0, -1);
            $vlcondominio = intval($vlcondominio);
            $vlcondominio = $vlcondominio / 100;
            $opluz = substr($vlluz, -1, 1);
            $vlluz = substr($vlluz, 0, -1);
            $vlluz = intval($vlluz);
            $vlluz = $vlluz / 100;
            $opagua = substr($vlagua, -1, 1);
            $vlagua = substr($vlagua, 0, -1);
            $vlagua = intval($vlagua);
            $vlagua = $vlagua / 100;
            $vliptu = intval($vliptu);
            $opiptu = '+';
            $vliptu = $vliptu / 100;
            $vldesc = intval($vldesc);
            $opdesc = '-';
            $vldesc = $vldesc / 100;
            $opgas = substr($vlgas, -1, 1);
            $vlgas = substr($vlgas, 0, -1);
            $vlgas = intval($vlgas);
            $vlgas = $vlgas / 100;
            $cliente2_id = intval($cliente2_id);
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            
            $sql .= "($id, $contrato_id, $numero, '$sequencia', $bem_id, $cliente_id, '$dtinicio', '$dtfim', 
                    '$dtvencto', $valor, $vlacrescido, '$dtpagto', $numrecibo, $vlpago, '$opjuros', $vljuros, 
                    '$opmulta', $vlmulta, '$opseguro', $vlseguro, '$opcondominio', $vlcondominio, '$opluz', $vlluz, 
                    '$opagua', $vlagua, '$opiptu', $vliptu, $numrecpro, '$opdesc', $vldesc, '$opgas', $vlgas, $cliente2_id)";
        }
        
        try
        {
            $conn-> query($sql);
            
            new TMessage('info', 'Arquivo importado com sucesso!');
        }
        catch (Exception $e) // in case of exception
        {            
            $j++;
            $target_folder = 'tmp/SQL/';
            $target_file   = $target_folder . $class . '.sql';
            $fp = fopen($target_file, "w");
 
            fwrite($fp, $sql);
            
            new TMessage('error', $e->getMessage());    
        }
        
        TTransaction::close();
    }
}