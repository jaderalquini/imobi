<?php
/**
 * Localizacao Active Record
 * @author  <your-name-here>
 */
class Localizacao extends TRecord
{
    const TABLENAME = 'localizacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
        
    private $municipio;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('municipio_id');
    }
    
    /**
     * Method get_municipio
     * Sample of usage: $localizacao->municipio->attribute;
     * @returns Municipio instance
     */
    public function get_municipio()
    {
        try
        {
            $obj = new Municipio($this->municipio_id);
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
        
        $sql = "INSERT INTO $table (id, descricao, municipio_id) values ";
           
        $primeiro = TRUE;       
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {
            $data = array_map('trim', explode(';', $linha));
            list($id, $descricao, $municipio_id) =  $data;
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$descricao', $municipio_id)";
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