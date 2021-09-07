<?php
/**
 * Movbancaria Active Record
 * @author  <your-name-here>
 */
class Movbancaria extends TRecord
{
    const TABLENAME = 'movbancaria';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}    
    
    private $banco;
    private $contacorrente;
    
    use SystemChangeLogTrait;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dtmov');
        parent::addAttribute('seq');
        parent::addAttribute('banco_id');
        parent::addAttribute('contacorrente_id');
        parent::addAttribute('tipo');
        parent::addAttribute('historico');
        parent::addAttribute('valor');
    }
    
    /**
     * Method get_banco
     * Sample of usage: $movbancaria->banco->attribute;
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
        
        $sql = "INSERT INTO $table (id, banco_id, contacorrente_id, dtmov, seq, tipo, historico, valor) values ";
        
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
                $sql = "INSERT INTO $table (id, banco_id, contacorrente_id, dtmov, seq, tipo, historico, valor) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($banco_id, $contacorrente_id, $dtmov, $seq, $tipo, $historico, $valor) =  $data;
            
            $i++;
            $id++;
            $dtmov = substr($dtmov,0,4)."-".substr($dtmov,4,2)."-".substr($dtmov,6,2);
            $historico = str_replace("'", "", $historico);
            $valor = intval($valor);
            $valor = $valor / 100;
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$banco_id', '$contacorrente_id', '$dtmov', $seq, '$tipo', '$historico', $valor)";
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
?>