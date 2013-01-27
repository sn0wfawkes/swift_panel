<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

function smarty_core_get_php_resource( &$params, &$smarty )
{
    $params['resource_base_path'] = $smarty->trusted_dir;
    $smarty->_parse_resource_name( $params, $smarty );
    if ( $params['resource_type'] == "file" )
    {
        $_readable = FALSE;
        if ( file_exists( $params['resource_name'] ) && is_readable( $params['resource_name'] ) )
        {
            $_readable = TRUE;
        }
        else
        {
            $_params = array(
                "file_path" => $params['resource_name']
            );
            require_once( SMARTY_CORE_DIR."core.get_include_path.php" );
            if ( smarty_core_get_include_path( $_params, $smarty ) )
            {
                $_include_path = $_params['new_file_path'];
                $_readable = TRUE;
            }
        }
    }
    else if ( $params['resource_type'] != "file" )
    {
        $_template_source = NULL;
        $_readable = is_callable( $smarty->_plugins['resource'][$params['resource_type']][0][0] ) && call_user_func_array( $smarty->_plugins['resource'][$params['resource_type']][0][0], array(
            $params['resource_name'],
            $_template_source,
            $smarty
        ) );
    }
    if ( method_exists( $smarty, "_syntax_error" ) )
    {
        $_error_funcc = "_syntax_error";
    }
    else
    {
        $_error_funcc = "trigger_error";
    }
    if ( $_readable )
    {
        if ( $smarty->security )
        {
            require_once( SMARTY_CORE_DIR."core.is_trusted.php" );
            if ( !smarty_core_is_trusted( $params, $smarty ) )
            {
                $smarty->$_error_funcc( "(secure mode) ".$params['resource_type'].":".$params['resource_name']." is not trusted" );
                return FALSE;
            }
        }
    }
    else
    {
        $smarty->$_error_funcc( $params['resource_type'].":".$params['resource_name']." is not readable" );
        return FALSE;
    }
    if ( $params['resource_type'] == "file" )
    {
        $params['php_resource'] = $params['resource_name'];
    }
    else
    {
        $params['php_resource'] = $_template_source;
    }
    return TRUE;
}

?>