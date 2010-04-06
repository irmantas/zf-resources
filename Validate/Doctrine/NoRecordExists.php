<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Irmis_Validate_Doctrine_NoRecordExists extends Zend_Validate_Db_Abstract
{
    public function  __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $options       = func_get_args();
            $temp['table'] = array_shift($options);
            $temp['field'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['adapter'] = array_shift($options);
            }

            $options = $temp;
        }

        if (empty($options['table'])) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Table option missing!');
        }

        if (empty($options['field'])) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Field option missing!');
        }

        $this->_table = $options['table'];
        $this->_field = $options['field'];
    }

    /**
     * Checks if given field does not exists in database
     * @param string $value
     * @return boolean
     */
    public function isValid ($value)
    {
        $valid = true;
        $value = trim($value);
        $this->_setValue($value);

        $q = Doctrine_Query::create()
            ->from(ucfirst($this->_table))
            ->where($this->_field . ' = ?', $value);

        $result = $q->execute();
        
        if ($result->count() > 0) {
            $valid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}