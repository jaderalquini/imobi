<?php
/**
 * Date validation
 *
 * @version    1.0
 * @package    validator
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDateValidator extends TFieldValidator
{
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation (ex: mask)
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $mask      = $parameters;
        $year_pos  = strpos($mask, 'yyyy');
        $month_pos = strpos($mask, 'mm');
        $day_pos   = strpos($mask, 'dd');
        
        $year      = substr($value, $year_pos, 4);
        $month     = substr($value, $month_pos, 2);
        $day       = substr($value, $day_pos, 2);
        
        if (!checkdate((int) $month, (int) $day, (int) $year))
        {
            throw new Exception(AdiantiCoreTranslator::translate("The filed ^1 is not a valid date", $label));
        }
    }
}
?>
