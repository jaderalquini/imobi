<?php
/**
 * Banco Active Record
 * @author  <your-name-here>
 */
class Banco extends TRecord
{
    const TABLENAME = 'banco';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    private $uf;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('agencia');
        parent::addAttribute('digito');
        parent::addAttribute('nome');
        parent::addAttribute('endereco');
        parent::addAttribute('cep');
        parent::addAttribute('municipio');
        parent::addAttribute('uf_id');
        parent::addAttribute('cnpj');
    }
    
    /**
     * Method get_uf
     * Sample of usage: $banco->uf->attribute;
     * @returns UF instance
     */
    public function get_uf()
    {
        try
        {
            $obj = new UF($this->uf_id);
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
        
        $sql = "INSERT INTO $table (id, codigo, agencia, digito, nome, endereco, cep, municipio, uf_id, cnpj) values ";
           
        $primeiro = TRUE;  
        $id = 0;      
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {
            $data = array_map('trim', explode(';', $linha));
            list($codigo, $agencia, $digito, $nome, $endereco, $cep, $municipio, $uf_id, $cnpj) =  $data;
            
            $id++;
            $codigo = str_pad($codigo, 3, '0', STR_PAD_LEFT);
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$codigo', '$agencia', '$digito', '$nome', '$endereco', '$cep', '$municipio', '$uf_id', '$cnpj')";
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