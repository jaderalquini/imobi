<?php
class Correcoes
{
    public function onCorrigir($param)
    {
        //new TMessage('info', json_encode($param));
        try
        {
            TTransaction::open($param['banco']);
            $conn = TTransaction::get();
                        
            $repository = new TRepository('Contrato');
            /*$criteria = new TCriteria;
            $criteria->add(new TFilter('diavencto', '>=', '1'));
            $criteria->add(new TFilter('diavencto', '<=', '9'));
            $objects = $repository->load($criteria);
            
            echo $criteria->dump();
            
            /*if ($objects)
            {
                foreach ($objects as $object)
                {
                    $dtinicio = TDate::date2us($object->dtinicio);
                    $dtinicio = date('Y-m-'.$diavencto, strtotime("+1 month", strtotime($dtinicio)));
                    $dtfim = TDate::date2us($object->dtfim);
                    $dtfim = date('Y-m-'.$diavencto, strtotime("+1 month", strtotime($dtfim)));
                    
                    $i = 0;
                    for ($date = strtotime($dtinicio); $date < strtotime($dtfim); $date = strtotime("+1 month", $date))                                                                                                                                                                                                                                           
                    {
                        $i++;
                        $repository = new TRepository('ParcelaReceber');
                        $criteria = new TCriteria;
                        $criteria->add(new TFilter('contrato_id', '=', $object->id));
                        $criteria->add(new TFilter('numero', '=', $i));
                        $parcelas = $repository->load($criteria);
                        
                        if ($parcelas)
                        {
                            foreach ($parcelas as $parcela)
                            {
                                $ppagar = new ParcelaReceber($parcela->id);
                                $ano = date('Y', $date);
                                $mes = date('m', $date);
                                $ppagar->dtvencto = $ano . '-' . $mes . '-' . $object->diapagto;
                                $ppagar->store();
                            }
                        }
                    }
                }
            }*/
            
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {            
            new TMessage('error', $e->getMessage());      
        }
    }
}