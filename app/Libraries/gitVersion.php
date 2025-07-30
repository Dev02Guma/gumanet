<?php
/**
 * Created by PhpStorm.
 * User: maryan.espinoza
 * Date: 10/10/2019
 * Time: 10:44
 */

class git_version
{
    public static  function get()
    {
        return sprintf('UNIMARK S.A © %s %s.%s', date('Y'), 'v1.'.date('y'), date('W'));
    }
}

