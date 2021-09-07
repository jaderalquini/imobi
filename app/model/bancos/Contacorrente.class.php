<?php
/**
 * Contacorrente Active Record
 * @author  <your-name-here>
 */
class Contacorrente extends TRecord
{
    const TABLENAME = 'contacorrente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
        
    private $banco;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('banco_id');
        parent::addAttribute('numero');
        parent::addAttribute('digito');
        parent::addAttribute('descricao');
    }
    
    /**
     * Method get_banco
     * Sample of usage: $contacorrente->banco->attribute;
     * @returns Banco instance
     */
    public function get_banco()
    {
        try
        {
            $bancos  = Banco::where('codigo', '=', $this->banco_id)->load();
            
            foreach ($bancos as $banco)
            {
                return $banco->nome;
            }  
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
        
        $sql = "INSERT INTO $table (id, banco_id, digito, descricao) values ";
           
        $primeiro = TRUE;
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {
            $data = array_map('trim', explode(';', $linha));
            list($banco_id, $id, $digito, $descricao) =  $data;
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "('$id', '$banco_id', '$digito', '$descricao')";
        }
        
        try
        {
            $conn-> query($sql);
            
            new TMessage('info', 'Arquivo importado com sucesso!');
        }
        catch (Exception $e) // in case of exception
        {            
            $target_folder = 'tmp/SQL/';
            $target_file   = $target_folder . $class . '.sql';
            $fp = fopen($target_file, "w");
 
            fwrite($fp, $sql);
            
            new TMessage('error', $e->getMessage());    
        }
        
        TTransaction::close();
    }   
}
?>