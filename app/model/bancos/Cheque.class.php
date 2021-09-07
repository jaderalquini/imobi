<?php
/**
 * Cheque Active Record
 * @author  <your-name-here>
 */
class Cheque extends TRecord
{
    const TABLENAME = 'cheque';
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
        parent::addAttribute('dtcheque');
        parent::addAttribute('banco_id');
        parent::addAttribute('contacorrente_id');
        parent::addAttribute('numero');
        parent::addAttribute('valor');
        parent::addAttribute('nominal');
        parent::addAttribute('dtprepago');
    }
    
    /**
     * Method get_banco
     * Sample of usage: $cheque->banco->attribute;
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
        
        $sql = "INSERT INTO $table (id, banco_id, contacorrente_id, dtcheque, numero, valor, nominal, dtprepago) values ";
           
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
                $sql = "INSERT INTO $table (id, banco_id, contacorrente_id, dtcheque, numero, valor, nominal, dtprepago) values ";
            }
            
            $data = array_map('trim', explode(';', $linha));
            list($banco_id, $contacorrente_id, $dtcheque, $numero, $valor, $nominal, $dtprepago) =  $data;
                        
            $i++;
            $id++;
            $dtcheque = substr($dtcheque,0,4)."-".substr($dtcheque,4,2)."-".substr($dtcheque,6,2);
            $tam = strlen($valor);
            $valor = substr($valor,0, $tam - 2);
            $nominal = str_replace("'", "", $nominal);
            $dtprepago = substr($dtprepago,0,4)."-".substr($dtprepago,4,2)."-".substr($dtprepago,6,2);
            
            if ($primeiro)
            {
                $primeiro = FALSE;
            }
            else
            {
                $sql .= ",";        
            }
            $sql .= "($id, '$banco_id', '$contacorrente_id', '$dtcheque', '$numero', $valor, '$nominal', '$dtprepago')";
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