<?php
namespace App\Controllers;

use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as Capsule;      //? conexion con la base de datos usando Query Builder

class assetsControler{
    /**
     * alert para usar sin ajaz, tiene timeout incluido con bootstrap
     */
    public static function alert($message, $typeAlert='info', $icon=''){
        if($message==NULL) return '';
        
        return '<div class="alert alert-'.$typeAlert.' alert-dismissible fade show" role="alert">
                    '.$icon.'
                    <div class="d-inline ms-1">
                        '.$message.'
                    </div>
                    <button id="btn_close_alert" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <script> window.onload = ()=>{ setTimeout(() => {btn_close_alert.click()}, 2600); }</script>';
    }

    /**
     * alert para usar con ajaz, no tiene timeout incluido con bootstrap
     */
    public static function alertAjax($message, $typeAlert='info', $icon=''){
        if($message==NULL) return '';
        
        return '<div class="alert alert-'.$typeAlert.' alert-dismissible fade show" role="alert">
                    '.$icon.'
                    <div class="d-inline ms-1">
                        '.$message.'
                    </div>
                    <button id="btn_close_alert" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    
    public static function sweetAlert($message, $icon='success', $title='Aviso'){
        return "
        Swal.fire(
            '$title',
            '$message',
            '$icon'
          )
        ";
    }
}