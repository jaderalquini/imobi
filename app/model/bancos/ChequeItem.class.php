<?php
/**
 * ChequeItem Active Record
 * @author  <your-name-here>
 */
class ChequeItem extends TRecord
{
    const TABLENAME = 'cheque_item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    private $banco;
    private $contacorrente;
    private $cheque;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('banco_id');
        parent::addAttribute('contacorrente_id');
        parent::addAttribute('cheque_id');
        parent::addAttribute('sequencia');
        parent::addAttribute('vltitulo');
        parent::addAttribute('vlpago');
        parent::addAttribute('variacao');
        parent::addAttribute('descricao');
    }
    
    /**
     * Method get_banco
     * Sample of usage: $cheque_item->banco->attribute;
     * @returns Banco instance
     */
    public function get_banco()
    {
        try
        {
            $obj = new Cheque($this->cheque_id);
            return $obj->numero;   
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
        
        $sql = "INSERT INTO $table (id, banco_id, contacorrente_id, cheque_id, sequencia, descricao, vltitulo, vlpago, variacao) values ";
           
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
                $sql = "INSERT INTO $table (id, banco_id, contacorrente_id, cheque_id, sequencia, descricao, vltitulo, vlpago, variacao) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($banco_id, $contacorrente_id, $cheque_id, $sequencia, $descricao, $vltitulo, $vlpago) =  $data;
            
            $i++;
            $id++;
            $descricao = str_replace("'","", $descricao);
            $variacao = 'NULL';
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$banco_id', '$contacorrente_id', '$cheque_id', $sequencia, '$descricao', $vltitulo, $vlpago, $variacao)";
        }
        
        //new TMessage('info', $sql);
        
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
?>