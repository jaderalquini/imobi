<?php
/**
 * ContacorrenteCliente Active Record
 * @author  <your-name-here>
 */
class ContacorrenteCliente extends TRecord
{
    const TABLENAME = 'contacorrente_cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
   
    private $cliente;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cliente_id');
        parent::addAttribute('banco');
        parent::addAttribute('praca');
        parent::addAttribute('numero');
        parent::addAttribute('digito');
        parent::addAttribute('agencia');
        parent::addAttribute('digitoagencia');
        parent::addAttribute('nomedep');
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $contacorrentecli->cliente->attribute;
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
        
        $sql = "INSERT INTO $table (id, cliente_id, banco, praca, numero, digito, agencia, digitoagencia, nomedep) values ";
           
        $primeiro = TRUE;
        $id = 0;       
        $linhas = file($target_file);
        foreach ($linhas as $linha) 
        {
            $data = array_map('trim', explode(';', $linha));
            list($cliente_id, $banco_id, $banco, $praca, $numero, $digito, $agencia, $digitoagencia, $nomedep) =  $data;
            
            $id++;
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, $cliente_id, '$banco', '$praca', '$numero', '$digito', '$agencia', '$digitoagencia', '$nomedep')";
        }
        
        //new TMessage('info', $sql);
        
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