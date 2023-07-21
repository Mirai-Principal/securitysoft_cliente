<?php

namespace App\Controllers;

use App\Models\notificaciones;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class DashboardController extends CoreController
{

    public function getReportarAction()
    {
        return $this->renderHTML('reportar.twig');
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
        $reportes = notificaciones::all();
        return $this->renderHTML('dashboard.twig', array('reportes' => $reportes));
    }
}
