<?php
/**
 * ParcelaPagar Active Record
 * @author  <your-name-here>
 */
class ParcelaPagar extends TRecord
{
    const TABLENAME = 'parcela_pagar';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
        
    private $bem;
    private $cliente;
    private $proprietario;
    
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
        parent::addAttribute('proprietario_id');
        parent::addAttribute('dtinicio');
        parent::addAttribute('dtfim');
        parent::addAttribute('dtvencto');
        parent::addAttribute('valor');
        parent::addAttribute('dtpagto');
        parent::addAttribute('numrecibo');
        parent::addAttribute('vlpago');
        parent::addAttribute('opcomissao');
        parent::addAttribute('vlcomissao');
        parent::addAttribute('opoutro');
        parent::addAttribute('vloutro');
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
        parent::addAttribute('opdevolucao');
        parent::addAttribute('vldevolucao');
        parent::addAttribute('opgas');
        parent::addAttribute('vlgas');
        parent::addAttribute('observacao');
    }
    
    /**
     * Method get_bem
     * Sample of usage: $parcela_pagar->bem->attribute;
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
     * Sample of usage: $parcela_pagar->cliente->attribute;
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
     * Method get_proprietario
     * Sample of usage: $contrato->proprietario->attribute;
     * @returns Cliente instance
     */
    public function get_proprietario()
    {
        try
        {
            $obj = new Cliente($this->proprietario_id);
            return $obj->nome;   
        }  
        catch (Exception $e) // in case of exception
        {
            return NULL;
        }      
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
        
        $sql = "INSERT INTO $table (id, contrato_id, numero, sequencia, bem_id, cliente_id, proprietario_id, dtinicio, 
                        dtfim, dtvencto, valor, dtpagto, numrecibo, vlpago, opcomissao, vlcomissao, opoutro, vloutro, opseguro, 
                        vlseguro, opluz, vlluz, opagua, vlagua, opiptu, vliptu, numrecpro, opdevolucao, vldevolucao, opgas, 
                        vlgas) values ";
           
        $primeiro = TRUE;
        $i = 0;
        $j = 0;
        $id = 0;
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {            
            if ($i == 50)
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
                $sql = "INSERT INTO $table (id, contrato_id, numero, sequencia, bem_id, cliente_id, proprietario_id, dtinicio, 
                        dtfim, dtvencto, valor, dtpagto, numrecibo, vlpago, opcomissao, vlcomissao, opoutro, vloutro, opseguro, 
                        vlseguro, opluz, vlluz, opagua, vlagua, opiptu, vliptu, numrecpro, opdevolucao, vldevolucao, opgas, 
                        vlgas) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($contrato_id, $numero, $sequencia, $bem_id, $cliente_id, $proprietario_id, $dtinicio, $dtfim, $dtvencto,
                $valor, $dtpagto, $numrecibo, $vlpago, $vlcomissao, $vloutro, $vlseguro, $vlcondominio, $vlluz, $vlagua,
                $vliptu, $numrecpro, $vldevolucao, $vlgas) =  $data;
            
            $i++;
            $id++;
            $contrato_id = intval($contrato_id);
            $numero = intval($numero);
            $bem_id = intval($bem_id);
            $cliente_id = intval($cliente_id);
            $proprietario = intval($proprietario_id);
            $dtinicio = substr($dtinicio,0,4)."-".substr($dtinicio,4,2)."-".substr($dtinicio,6,2);
            $dtfim = substr($dtfim,0,4)."-".substr($dtfim,4,2)."-".substr($dtfim,6,2);
            $dtvencto = substr($dtvencto,0,4)."-".substr($dtvencto,4,2)."-".substr($dtvencto,6,2);
            $valor = intval($valor);
            $valor = $valor / 100;
            if ($dtpagto != '0') 
            {
                $dtpagto = substr($dtpagto,0,4)."-".substr($dtpagto,4,2)."-".substr($dtpagto,6,2);                         
            }
            $numrecibo = intval($numrecibo);
            $vlpago = intval($vlpago);
            $vlpago = $vlpago / 100;
            $vlcomissao = intval($vlcomissao);
            $opcomissao = '-';
            $vlcomissao = $vlcomissao / 100;            
            $opoutro = substr($vloutro, -1, 1);
            $vloutro = substr($vloutro, 0, -1);
            $vloutro = intval($vloutro);
            $vloutro = $vloutro / 100;
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
            $opiptu = substr($vliptu, -1, 1);
            $vliptu = substr($vliptu, 0, -1);
            $vliptu = intval($vliptu);
            $vliptu = $vliptu / 100;
            $numrecpro = intval($numrecpro);
            $opdevolucao = '-';
            $vldevolucao = intval($vldevolucao);
            $vldevolucao = $vldevolucao / 100;
            $opgas = substr($vlgas, -1, 1);
            $vlgas = substr($vlgas, 0, -1);
            $vlgas = intval($vlgas);
            $vlgas = $vlgas / 100;
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            
            $sql .= "($id, $contrato_id, $numero, '$sequencia', $bem_id, $cliente_id, $proprietario_id, '$dtinicio', 
                    '$dtfim', '$dtvencto', $valor, '$dtpagto', $numrecibo, $vlpago, '$opcomissao', $vlcomissao, '$opoutro', 
                    $vloutro, '$opseguro', $vlseguro, '$opluz', $vlluz, '$opagua', $vlagua, '$opiptu', $vliptu, $numrecpro, 
                    '$opdevolucao', $vldevolucao, '$opgas', $vlgas)";
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
            $target_file   = $target_folder . $class . $j . '.sql';
            $fp = fopen($target_file, "w");
 
            fwrite($fp, $sql);
            
            new TMessage('error', $e->getMessage());    
        }
        
        TTransaction::close();  
    }
}