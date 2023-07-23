<?php

namespace App\Controllers;

use App\Models\notificaciones;

use Illuminate\Database\Capsule\Manager as Capsule;      //? conexion con la base de datos usando Query Builder
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class DashboardController extends CoreController
{

    public function getReportarAction()
    {
        return $this->renderHTML('reportar.twig');
    }

    public function postEstadoAction(ServerRequest $request) {
        $responseMessage = null;    //var para recuperar los mesajes q suceda durante la ejecucion
        $tipoRespuesta = null; //var para guardar el tipo de respuesta - icono

        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();

            $validator = v::key('estado', v::stringType()->notEmpty());
            
            try {
                $validator->assert($postData);

                $id = $postData['id'];
                $notificacion = notificaciones::find($id);
                if($notificacion){
                    Capsule::beginTransaction();

                    $notificacion->estado = $postData['estado'];
                    $notificacion->save();

                    Capsule::commit();

                    $responseMessage = 'Se ha actualizado';
                    $tipoRespuesta = "success";
                }
                else{
                    $responseMessage = 'Ha ocurrido un error! Informe a soporte ';
                    $tipoRespuesta = "error";
                }
            } catch (\Exception $e) {
                // $responseMessage = $e->getMessage();
                Capsule::rollback();
                $responseMessage = $e->getCode();
                $responseMessage = 'Ha ocurrido un error! Informe a soporte '. $responseMessage;
                $tipoRespuesta = "error";
            }
        }

        $respuesta = array(
            'responseMessage' => $responseMessage,
            'tipoRespuesta' => $tipoRespuesta
        );
        return $this->jsonReturn($respuesta);
    }

    public function postFormNotificacionAction(ServerRequest $request)
    {
        $responseMessage = null;    //var para recuperar los mesajes q suceda durante la ejecucion

        if ($request->getMethod() == "POST") {
            $postData = $request->getParsedBody();

            $passMasterValidator = v::key('emergenciaTipo', v::stringType()->notEmpty());

            try {
                $passMasterValidator->assert($postData);

                $notificaciones = new notificaciones();

                $notificaciones->tipo = $postData['emergenciaTipo'];
                $notificaciones->save();
                $responseMessage = 'Se ha guardado con éxito';
                $headers = array(
                    'responseMessage' => $responseMessage
                );

                return new RedirectResponse('/reportado', 301, $headers);

            } catch (\Exception $e) {
                // $responseMessage = $e->getMessage();
                $responseMessage = 'Ha ocurrido un error! Informe a soporte';
            }
        }
    }


    public function getDashboardAction()
    {
        $reportes = notificaciones::where('estado', '!=', 'Finalizado')->get();
        return $this->renderHTML('dashboard.twig', array(
            'reportes' => $reportes,
            "session" => $_SESSION
        ));
    }
}
