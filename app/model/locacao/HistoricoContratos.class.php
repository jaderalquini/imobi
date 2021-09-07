<?php
/**
 * HistoricoContratos Active Record
 * @author  <your-name-here>
 */
class HistoricoContratos extends TRecord
{
    const TABLENAME = 'historico_contratos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    private $bem;
    private $contrato;
    private $proprietario;
    private $cliente;
    private $cliente2;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('bem_id');
        parent::addAttribute('contrato_id');
        parent::addAttribute('proprietario_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('liq_cliente');
        parent::addAttribute('liq_proprietario');
        parent::addAttribute('cliente2_id');
    }
    
    /**
     * Method get_bem
     * Sample of usage: $historico_contratos->bem->attribute;
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
    
    /**
     * Method get_cliente
     * Sample of usage: $historico_contratos->cliente->attribute;
     * @returns Cliente instance
     */
    public function get_cliente()
    {
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
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
        
        $sql = "INSERT INTO $table (id, bem_id, contrato_id, proprietario_id, cliente_id, liq_cliente, liq_proprietario, 
                cliente2_id) values ";
           
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
                $sql = "INSERT INTO $table (id, bem_id, contrato_id, proprietario_id, cliente_id, liq_cliente, liq_proprietario, 
                        cliente2_id) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($bem_id, $contrato_id, $proprietario_id, $cliente_id, $liq_cliente, $liq_proprietario, $cliente2_id) =  $data;
            
            $data = explode(";", $linha);
            $data = str_replace("'","",$data);
            
            $i++;
            $id++;
            if ($liq_cliente == 1)
            {
                $liq_cliente = 'S';
            }            
            if ($liq_cliente == 0)
            {
                $liq_cliente = 'N';
            }
            if ($liq_proprietario == 1)
            {
                $liq_proprietario = 'S';
            }
            if ($liq_proprietario == 0)
            {
                $liq_proprietario = 'N';    
            }            
            $cliente2_id = intval($cliente2_id); 
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, $bem_id, $contrato_id, $proprietario_id, $cliente_id, '$liq_cliente', '$liq_proprietario', 
                    $cliente2_id)";       
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